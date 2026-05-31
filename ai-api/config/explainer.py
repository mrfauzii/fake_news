import shap

_explainer = None

def get_text_explainer(model):
    global _explainer

    if _explainer is None:
        _explainer = shap.TreeExplainer(model)

    return _explainer