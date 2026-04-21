from .classification_service import classify, load_classify_model

def predict(similarity_score, avg_date_scaled):
    try:
        classify_models = load_classify_model()
        
        x_data = [[similarity_score, avg_date_scaled]]
        predictions = classify(classify_models, x_data)
        
        return predictions

    except Exception as e:
        return {"error": f"Prediction failed: {str(e)}"}