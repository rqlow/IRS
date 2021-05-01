from PIL import Image
import matplotlib.pyplot as plt
import pytesseract
from pytesseract import Output
import argparse
import cv2
from skimage.filters import threshold_local
import os
import numpy as np
import re

from py.ocr.src.configs import keywords, IMG_DIR, OCR_IMG_DIR, OCR_TEXT_DIR


class ImageReader:
    def read(self, filepath, save_raw_text=False, save_img=False):

        if os.path.isfile(filepath):

            self.filepath = filepath

            fn = filepath.rsplit("/")[-1]
            self.filename = fn

        else:

            raise Exception("File does not exist")

        self.image = self.load_image(filepath)

        img_processed = self.simple_process(self.image)

        if save_img:

            path = self.filename.rsplit(".")[-2] + "_ocr.png"
            path = os.path.join(OCR_IMG_DIR, path)
            cv2.imwrite(path, img_processed)

            # print("saving image to: ", path)

        ## next parse to ocr and do some simple verification with keyword matching
        ## extract text data
        extracted_text = self.extract_text_data(img_processed, save_raw_text)
        
        if not extracted_text:
            # print("The image could not be read. Try again.")
            return

        return extracted_text

    def load_image(self, filepath):

        image = cv2.imread(filepath)

        return image

    def simple_process(self, image):

        ## Simple processing steps:
        # - convert to grey
        # - threshold to bw
        # - morph to remove noise
        # - extract largest contour  (assuming receipt is the largest contour in the image)
        # - crop with bounding rectangle on original image
        # - convert to gray and threshold

        original = image.copy()

        # convert to gray
        gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)

        # threshold
        thresh = cv2.threshold(gray, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)[1]

        # morph to remove noise
        kernel = cv2.getStructuringElement(cv2.MORPH_ELLIPSE, (11, 11))
        morphed = cv2.morphologyEx(thresh, cv2.MORPH_CLOSE, kernel)

        # get largest contour
        cnts = cv2.findContours(morphed, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)[-2]
        cnt = sorted(cnts, key=cv2.contourArea)[-1]

        # Crop with bounding rectangle on largest contour
        x, y, w, h = cv2.boundingRect(cnt)
        cropped = image[y : y + h, x : x + w]

        # convert to gray and threshold

        cropped_gray = cv2.cvtColor(cropped, cv2.COLOR_BGR2GRAY)

        T = threshold_local(cropped_gray, 21, offset=5, method="gaussian")

        processed = (cropped_gray > T).astype("uint8") * 255

        return processed

    def extract_text_data(self, image, save_raw_text=False):

        extracted_text = pytesseract.image_to_string(image, lang="eng")

        if save_raw_text:

            ## Saves the output txt to the same directory
            output_filepath = self.filename.rsplit(".")[-2] + "_ocr.txt"
            output_filepath = os.path.join(OCR_TEXT_DIR, output_filepath)

            # with open(output_filepath, "w") as f:
                # print("saving file to: ", output_filepath)
                # f.write(extracted_text)

        # return directly without checking
        return extracted_text


if __name__ == "__main__":

    filepath = "images/receipt1.jpeg"

    img_reader = ImageReader(filepath)
    img_reader.read(save_raw_text=True)
