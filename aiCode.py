import numpy as np
from tensorflow.keras.models import load_model
from tensorflow.keras.preprocessing.sequence import pad_sequences
from tensorflow.keras.models import Model
from tensorflow.keras.applications.inception_v3 import InceptionV3, preprocess_input
from pickle import load
from tensorflow.keras.preprocessing.image import load_img, img_to_array
import requests
from io import BytesIO

# Load the trained model to classify sign
base_model = InceptionV3(
    weights='inception_v3_weights_tf_dim_ordering_tf_kernels.h5')
vgg_model = Model(base_model.input, base_model.layers[-2].output)


def preprocess_img_from_url(url):
    # Inception V3 expects images in 299x299 size
    response = requests.get(url, verify=False)
    img = load_img(BytesIO(response.content), target_size=(299, 299))
    x = img_to_array(img)
    # Add one more dimension
    x = np.expand_dims(x, axis=0)
    x = preprocess_input(x)
    return x


def encode(image):
    image = preprocess_img_from_url(image)
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


# def beam_search(image, beam_index=3):
#     start = [wordtoix["startseq"]]
#     # ... (rest of the function)
#     # start_word[0][0] = index of the starting word
#     # start_word[0][1] = probability of the word predicted
#     start_word = [[start, 0.0]]

#     while len(start_word[0][0]) < max_length:
#         temp = []
#         for s in start_word:
#             par_caps = pad_sequences([s[0]], maxlen=max_length)
#             e = image
#             preds = model.predict([e, np.array(par_caps)])

#             # Getting the top <beam_index>(n) predictions
#             word_preds = np.argsort(preds[0])[-beam_index:]

#             # creating a new list so as to put them via the model again
#             for w in word_preds:
#                 next_cap, prob = s[0][:], s[1]
#                 next_cap.append(w)
#                 prob += preds[0][w]
#                 temp.append([next_cap, prob])

#         start_word = temp
#         # Sorting according to the probabilities
#         start_word = sorted(start_word, reverse=False, key=lambda l: l[1])
#         # Getting the top words
#         start_word = start_word[-beam_index:]

#     start_word = start_word[-1][0]
#     intermediate_caption = [ixtoword[i] for i in start_word]

#     final_caption = []

#     for i in intermediate_caption:
#         if i != 'endseq':
#             final_caption.append(i)
#         else:
#             break

#     final_caption = ' '.join(final_caption[1:])
#     return final_caption


# Load the model (new-model-1.h5) for generating captions
model = load_model('new-model-1.h5')

# Example usage for generating captions for an image from URL
image_url = "https://localhost/image/test-images/IMG_1607.jpg"
enc = encode(image_url)
image = enc.reshape(1, 2048)

# Generate captions using greedy search
pred = greedy_search(image)
print("Greedy Search:", pred)

# # Generate captions using beam search
# beam_3 = beam_search(image)
# print("Beam Search (Beam Index = 3):", beam_3)

# beam_5 = beam_search(image, 5)
# print("Beam Search (Beam Index = 5):", beam_5)
