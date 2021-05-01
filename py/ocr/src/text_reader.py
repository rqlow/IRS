import pytesseract
from pytesseract import Output

import nltk
from nltk.corpus import wordnet as wn

from spellchecker import SpellChecker

import re
import json

import mysql.connector

from py.ocr.src.configs import (
    price_related_words,
    host,
    port,
    user,
    password,
    database,
)


class TextReader:
    def __init__(self):

        self.date_pattern = re.compile(
            r"(0[1-9]|[12][0-9]|3[01])[/](0[1-9]|1[012])[/](20|21)"
        )

        self.price_pattern = re.compile("\d{1,3}[\.,]\d\d")

        # negative prices
        self.promo_price_pattern = re.compile("\-\d{1,3}[\.,]\d\d")

        # get the knowledge base of food words from db
        self.master_list, self.tag_to_id = self.get_master_list_from_db()

        self.spellchecker = SpellChecker(language=None)

        # build spell checker using corpus of food words from db
        self.spellchecker.word_frequency.load_words(self.master_list)

    def read(self, extracted_text):

        self.extracted_text = extracted_text.lower()

        self.lines = self.extracted_text.splitlines()

        self.date = self.get_date()

        self.item_list = list(filter(None, self.get_item_lines()))

        found_items, unknown_items, lines_ignored = self.extract_items()

        return found_items, unknown_items

    def get_master_list_from_db(self):

        mydb = mysql.connector.connect(
            host=host, port=port, user=user, password=password, database=database
        )

        mycursor = mydb.cursor(buffered=True)

        sql = "SELECT food_tag, food_id FROM musteatnow.stock_tag"

        mycursor.execute(sql)

        tag_to_id = dict()
        for row in mycursor:

            if row[0].lower() not in tag_to_id.keys():
                tag_to_id[row[0].lower()] = [str(row[1])]

            else:
                tag_to_id[row[0].lower()].append(str(row[1]))

        mycursor.close()
        mydb.close()

        master_list = tag_to_id.keys()

        return master_list, tag_to_id

    def get_date(self):

        has_date = self.date_pattern.search(self.extracted_text)

        if has_date:
            date = has_date.group()
        else:
            date = None

        return date

    def get_item_lines(self):

        # crop off top and bottom to return the sectiion with purchase items

        lines = self.lines

        for i, line in enumerate(self.lines):

            # match with keywords that identify the end of item listing
            if any(word in line for word in ["total", "master", "visa"]):
                # if "total","" in line:
                total_line = i
                break

        for i, line in enumerate(self.lines):
            has_price = self.price_pattern.search(line)

            if has_price:
                price = has_price.group()
                first_line_with_price = i
                break

        # could not properly identify region of lines. parse all lines.
        if first_line_with_price > total_line:
            item_list = lines
            return item_list

        # could not find line with total price
        if total_line is None:

            # grab all lines starting from first line with price
            if first_line_with_price is not None:
                item_list = lines[first_line_with_price - 1 :]
                return item_list

            # both total line and first line are not identified
            else:
                item_list = lines
                return item_list

        else:
            # both are identified
            if first_line_with_price is not None:
                item_list = lines[first_line_with_price - 1 : total_line + 1]
                return item_list

            # could not find the first price line
            else:
                item_list = lines
                return item_list

    def extract_items(self):

        item_list = self.item_list
        found_items = {}
        unknown_items = {}

        lines_to_process = list(range(len(item_list)))
        lines_ignored = []

        for i, line in enumerate(item_list):

            if i not in lines_to_process:
                continue

            line_status = self.infer_line_status(line)
            price = 0
            qty = 1

            if line_status["ignore"]:
                lines_ignored.append(line)
                continue

            ## if has price AND has food item: add to dict and quantity = 1
            if line_status["has_price"] and line_status["has_food_item"]:

                price = line_status["price"]
                # '.' and ',' are commonly misread by the ocr. replace any , in the price with . and convert to float
                price = float(price.replace(",", "."))
                qty = 1
                lines_to_process.remove(i)

            ## if it has food items only, price and quantity may be in next line

            elif line_status["has_food_item"]:

                next_line = item_list[i + 1]
                next_line_status = self.infer_line_status(next_line)

                # next line has price only. then match this price and quantity to the food item
                if next_line_status["has_price"] and (
                    not next_line_status["has_food_item"]
                ):

                    qty = self.infer_quantity(next_line)
                    price = next_line_status["price"]
                    price = float(price.replace(",", "."))
                    lines_to_process.remove(i)
                    lines_to_process.remove(i + 1)

            else:

                line_status["ignore"] = True
                lines_ignored.append(line)

            if not line_status["ignore"]:

                if "food_word" in line_status.keys():

                    food_word = line_status["food_word"]

                    rowid = len(found_items.keys()) + 1

                    found_items[rowid] = {}
                    found_items[rowid]["food_tag"] = food_word

                    ids = self.tag_to_id[food_word]

                    found_items[rowid]["food_id"] = ", ".join(ids)
                    found_items[rowid]["full_text"] = line_status["full_text"]
                    found_items[rowid]["price"] = price
                    found_items[rowid]["quantity"] = qty

                elif "unknown_item" in line_status.keys():
                    item = line_status["unknown_item"]

                    unknown_id = len(unknown_items.keys()) + 1

                    unknown_items[unknown_id] = {}
                    unknown_items[unknown_id]["item"] = item
                    unknown_items[unknown_id]["full_text"] = line_status["full_text"]
                    unknown_items[unknown_id]["price"] = price
                    unknown_items[unknown_id]["quantity"] = qty

                else:
                    raise Exception("has price and food but can't find any item to add")

        return found_items, unknown_items, lines_ignored

    def infer_unknown_item(self, line):

        if self.is_price_only(line):
            # no food item
            return

        words = line.split()
        # choose the word with longest length
        item = max(words, key=len)

        alpha = sum(c.isalpha() for c in item)

        # if it has few characters, assume it is not an item
        if (alpha / (len(item))) < 0.7:
            return

        # if it begins with a digit, assume not item
        if item[0].isdigit():
            return

        checked_item = self.check_spelling([item])

        return checked_item

    def infer_quantity(self, line):

        quantity = 1

        # split words and digits and grab the first one
        ret = re.findall(r"[^\W\d_]+|\d+", line)

        if ret:
            # if the first one is a digit, assign it as the quantity
            if ret[0].isdigit():
                quantity = str(max(1, int(ret[0])))

        return quantity

    def infer_line_status(self, line):

        status = dict()
        status["has_price"] = False
        status["has_food_item"] = False
        status["ignore"] = False

        promo = self.promo_price_pattern.search(line)

        if promo:
            status["ignore"] = True
            return status

        # if len(line) > 50:
        #    status["ignore"] = True
        #    return status

        # substitute special characters
        line = re.sub("[^A-Za-z0-9,\./\- ]+", "", line)

        # search if line has price
        words = line.split()
        for word in words:

            ret = self.price_pattern.match(word)
            if ret:
                status["price"] = ret.group()
                status["has_price"] = True
                words.remove(word)

        text_line = " ".join(words)

        # split all words and digits
        split_words = re.findall(r"[^\W\d_]+|\d+", line)

        food_words = []

        # search if word matches with food item list / database
        for word in split_words:

            if word in self.master_list:
                status["has_food_item"] = True
                food_word = word
                food_words.append(word)

                status["food_word"] = food_word
                status["full_text"] = text_line

        # if no food item is identified then we try to infer if there might be a new / unknown item
        if not status["has_food_item"]:

            item = self.infer_unknown_item(line)

            if item is not None:

                # check the returned item again with the database because infer_unknown runs a spell check
                if item in self.master_list:
                    food_word = item
                    status["food_word"] = food_word
                    status["full_text"] = text_line
                    status["has_food_item"] = True

                else:
                    status["unknown_item"] = item
                    status["full_text"] = text_line
                    status["has_food_item"] = True

        return status

    """ # DEPRECATED. using our own knowledge base instead of wordnet knowledge base
    def is_food(self, word):
        # using nltk synsets
        syns = wn.synsets(str(word), pos=wn.NOUN)

        for syn in syns:

            if "food" in syn.lexname():
                return True

            else:
                return False """

    def is_price_only(self, line):

        # checks if the line likely contains only price and no items

        split_words = re.findall(r"[^\W\d_]+|\d+", line)

        # simple filter / keyword matching
        intersection = list(set(price_related_words) & set(split_words))

        if len(intersection) > 0:
            ## likely another price line with no items.
            return True

        # count the number of alpha characters in the line
        alpha = 0
        alpha = sum(c.isalpha() for c in line)

        # if it has few characters, assume no items to extract
        if alpha / (len(line)) < 0.5:
            return True

        else:
            # likely has some unknown items in the line
            return False

    def check_spelling(self, words):

        ## simple spell check

        misspelled = self.spellchecker.unknown(words)

        new_words = words

        for word in misspelled:

            corrected_word = self.spellchecker.correction(word)

            new_words.remove(word)
            new_words.append(corrected_word)

        assert len(new_words) == 1

        return new_words[0]


if __name__ == "__main__":
    with open("ocr_text/receipt1_ocr.txt", "r") as f:
        extracted_text = f.read()

    tr = TextReader()

    print("read")
    tr.read(extracted_text)