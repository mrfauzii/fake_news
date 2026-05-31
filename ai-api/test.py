import joblib
import numpy as np
from config.explainer import get_text_explainer
# load model

model = joblib.load("./models/cat_model (5).pkl")

explainer = get_text_explainer(model)
# print(explainer.model.feature_names)
print(type(model))

if hasattr(model, "feature_names_in_"):
    print(model.feature_names_in_)

if hasattr(model, "feature_names_"):
    print(model.feature_names_)

if hasattr(model, "n_features_in_"):
    print(model.n_features_in_)

# # susun fitur (HARUS urut sesuai training)
# X = [[
#         0.044,	0.5541,	5.0, 0.3242067575410226, 0.28137783094200997
# ]]

# # prediksi
# pred = model.predict(X)
# proba = model.predict_proba(X)

# print("Prediction:", pred[0])
# print("Confidence:", max(proba[0]))
# print(proba)
# print(model.classes_)

# features = [
#     "mean_entailment",
#     "mean_contradiction",
#     "std_contradiction",
#     "time_confidence",
#     "title_confidence"
# ]

# X_user = X[0]

# importance_scores = model.feature_importances_

# contrib = []
# for i in range(len(features)):
#     score = X_user[i] * importance_scores[i]
#     contrib.append((features[i], score))
    
# print(contrib)

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


# from google import genai
# import os
# import dotenv
# dotenv.load_dotenv()
# API_KEY = os.getenv("API_KEY")


# client = genai.Client(api_key=API_KEY)

# for model in client.models.list():
#     print(model.name)