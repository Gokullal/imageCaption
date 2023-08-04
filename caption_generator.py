# caption_generator.py
import sys
from PIL import Image
import requests
from io import BytesIO
import numpy as np
import cv2
from tensorflow.keras.models import load_model
from tensorflow.keras.preprocessing.sequence import pad_sequences
from tensorflow.keras.models import Model
from tensorflow.keras.applications.inception_v3 import InceptionV3, preprocess_input
from pickle import load
from tensorflow.keras.preprocessing.image import load_img, img_to_array

base_model = InceptionV3(
    weights='inception_v3_weights_tf_dim_ordering_tf_kernels.h5')
vgg_model = Model(base_model.input, base_model.layers[-2].output)


def preprocess_img(img_path):
    # inception v3 excepts img in 299*299
    img = load_img(img_path, target_size=(299, 299))
    x = img_to_array(img)
    # Add one more dimension
    x = np.expand_dims(x, axis=0)
    x = preprocess_input(x)
    return x


def encode(image):
    image = preprocess_img(image)
    vec = vgg_model.predict(image)
    vec = np.reshape(vec, (vec.shape[1]))
    return vec


pickle_in = open("wordtoix.pkl", "rb")
wordtoix = load(pickle_in)
pickle_in = open("ixtoword.pkl", "rb")
ixtoword = load(pickle_in)
max_length = 74


def greedy_search(pic):
    start = 'startseq'
    for i in range(max_length):
        seq = [wordtoix[word] for word in start.split() if word in wordtoix]
        seq = pad_sequences([seq], maxlen=max_length)
        yhat = model.predict([pic, seq])
        yhat = np.argmax(yhat)
        word = ixtoword[yhat]
        start += ' ' + word
        if word == 'endseq':
            break
    final = start.split()
    final = final[1:-1]
    final = ' '.join(final)
    return final


def beam_search(image, beam_index=3):
    start = [wordtoix["startseq"]]
    # ... (rest of the function)


if __name__ == "__main__":
    # Load the model for generating captions
    model = load_model('new-model-1.h5')

    # Get the image URL from the PHP argument
    image_url = sys.argv[1]

    try:
        response = requests.get(image_url)
        uploaded = Image.open(BytesIO(response.content))
        enc = encode(BytesIO(response.content))
        image = enc.reshape(1, 2048)

        # Generate captions using greedy search
        pred = greedy_search(image)

        # Generate captions using beam search
        beam_3 = beam_search(image)
        beam_5 = beam_search(image, 5)

        # Print the results
        print(pred + '\n' + beam_3 + '\n' + beam_5)
    except:
        print("", "", "")
