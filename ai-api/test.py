# import joblib
# import numpy as np

# # load model
# model = joblib.load("./models/rf_model.pkl")


# # susun fitur (HARUS urut sesuai training)
# X = [[
#         0.6967,	0.0513,	0.043902,	0.856553,	0.059734
# ]]

# # prediksi
# pred = model.predict(X)
# proba = model.predict_proba(X)

# print("Prediction:", pred[0])
# print("Confidence:", max(proba[0]))
# print(proba)
# print(model.classes_)
# ==========================================
# CEK DATA COLLECTION
# ==========================================

# from config.chroma_config import get_chroma_collection

# text_request = get_chroma_collection("text_request")
# def check_collection_data(collection):

#     try:

#         data = collection.get()

#         return {
#             "status": "success",
#             "total_data": len(data.get("ids", [])),
#             "ids": data.get("ids", [])
#         }

#     except Exception as e:

#         return {
#             "status": "error",
#             "message": str(e)
#         }
        
# result = check_collection_data(text_request)

# print(result)


from google import genai
import os
import dotenv
dotenv.load_dotenv()
API_KEY = os.getenv("API_KEY")


client = genai.Client(api_key=API_KEY)

for model in client.models.list():
    print(model.name)