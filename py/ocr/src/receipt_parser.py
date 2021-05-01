import os
import time
import json

from py.ocr.src.image_reader import ImageReader
from py.ocr.src.text_reader import TextReader
from py.ocr.src.configs import OCR_TEXT_DIR, OCR_IMG_DIR


class ReceiptParser:
    def __init__(self, filepath, save_raw_text=False, save_img=False):

        if os.path.isfile(filepath):

            self.filepath = filepath

        else:

            raise Exception("File does not exist")

        self.save_raw_text = save_raw_text
        self.save_img = save_img
        self.image_reader = ImageReader()
        self.text_reader = TextReader()

        self.found_items = None
        self.unknown_items = None
        self.date = None
        self.init_dirs()

    def parse(self):

        self.extracted_text = self.image_reader.read(
            self.filepath, self.save_raw_text, self.save_img
        )

        if self.extracted_text is not None:

            found_items, unknown_items = self.text_reader.read(self.extracted_text)
            self.date = self.text_reader.date
            # print("Date found: ", self.date)
            self.found_items = found_items
            self.unknown_items = unknown_items

        else:
            # print("No text found")
            pass

        output = dict()
        output["food_items"] = dict(found_items)
        output["unknown"] = dict(unknown_items)
        output_json = json.dumps(output, indent=4)

        return output_json

    def init_dirs(self):

        dirs = [OCR_TEXT_DIR, OCR_IMG_DIR]

        for dir in dirs:
            os.makedirs(dir, exist_ok=True)


if __name__ == "__main__":

    ts = time.time()
    filepath = "images/receipt1.jpeg"
    rp = ReceiptParser(filepath)
    rp.parse()
    te = time.time()
    # print("total time: ", te - ts)
