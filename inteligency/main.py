import joblib
from fastapi import FastAPI
from pydantic import BaseModel

app = FastAPI()

# Load models securely
expertise_model = joblib.load("expertise_model.pkl")
risk_model = joblib.load("risk_model.pkl")

# Hardcoded rules for safety fallback bypass logic
HIGH_RISK_PHRASES = [
    "want to die",
    "kill myself",
    "suicide",
    "end my life"
]

# 1. Define the expected request body structure
class AnalyzeRequest(BaseModel):
    text: str

# 2. Optimized routing logic
@app.post("/analyze")
def analyze(data: AnalyzeRequest):
    text = data.text 

    # Safety First: Match static crisis keywords immediately
    for phrase in HIGH_RISK_PHRASES:
        if phrase in text.lower():
            return {
                "expertise": "Mental Health",
                "risk_level": "Critical",
                "confidence": 1.0
            }

    # Extract probabilities first to get confidence safely
    expertise_probabilities = expertise_model.predict_proba([text])[0]
    confidence = float(max(expertise_probabilities))

    # Fix: Probabilities range from 0.0 to 1.0. 
    # Here we filter out predictions under 50% confidence.
    if confidence < 0.5:
        return {
            "message": "Couldn't accurately determine expertise. Please rephrase or specify your needs more clearly."
        }
    

    # Compute actual predictions since we passed the confidence threshold
    expertise = expertise_model.predict([text])[0]
    risk = risk_model.predict([text])[0]

    return {
        "expertise": str(expertise),
        "risk_level": str(risk),
        "confidence": confidence
    }

@app.get("/test")
def test():
    return {"status": "working"}