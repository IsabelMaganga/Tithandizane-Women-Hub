from fastapi import FastAPI
import joblib
from pydantic import BaseModel

app = FastAPI()

expertise_model = joblib.load("expertise_model.pkl")
risk_model = joblib.load("risk_model.pkl")

HIGH_RISK_PHRASES = [
    "want to die",
    "kill myself",
    "suicide",
    "end my life"
]






# 1. Define the expected request body structure
class AnalyzeRequest(BaseModel):
    text: str

# ... your models and phrases here ...

# 2. Use the schema in your route
@app.post("/analyze")
def analyze(data: AnalyzeRequest):

    # Access the property directly via dot notation
    text = data.text 

    for phrase in HIGH_RISK_PHRASES:
        if phrase in text.lower():
            return {
                "expertise": "Mental Health",
                "risk_level": "Critical",
                "confidence": 1.0
            }

    expertise = expertise_model.predict([text])[0]
    risk = risk_model.predict([text])[0]

    confidence = max(
         expertise_model.predict_proba([text])[0]
    )

    return {
        "expertise": expertise,
        "risk_level": risk,
        "confidence": float(confidence)
    }

@app.get("/test")
def test():
    return {"status": "working"}