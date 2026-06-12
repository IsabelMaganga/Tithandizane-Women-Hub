from fastapi import FastAPI
import joblib

app = FastAPI()

expertise_model = joblib.load("expertise_model.pkl")
risk_model = joblib.load("risk_model.pkl")

HIGH_RISK_PHRASES = [
    "want to die",
    "kill myself",
    "suicide",
    "end my life"
]


@app.post("/analyze")
def analyze(data: dict):

    text = data["text"]

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