import os
import argparse

from py.ocr.src.receipt_parser import ReceiptParser

ap = argparse.ArgumentParser()

ap.add_argument("path", help="path to input image to be OCR")
ap.add_argument(
    "--save_text",
    help="set this flag to save the raw text output",
    default=False,
    action="store_true",
)

ap.add_argument(
    "--save_img",
    help="set this flag to save the raw text output",
    default=False,
    action="store_true",
)


args = vars(ap.parse_args())
# print(args)

save_raw_text = args["save_text"]
save_img = args["save_img"]

# create the ReceiptParser object. filepath is required.
# saving raw text/img is optional, defaults to false
receipt_parser = ReceiptParser(args["path"], save_raw_text, save_img)

# parse method returns found items and unknown items in json format
output = receipt_parser.parse()
print(output)

# date can be accessed as an attribute
date = receipt_parser.date
# print("Date extracted:", date)
