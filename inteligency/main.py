import joblib
from fastapi import FastAPI
from pydantic import BaseModel

app = FastAPI()

expertise_model = joblib.load("expertise_model.pkl")
risk_model      = joblib.load("risk_model.pkl")


HIGH_RISK_PHRASES = [
    # direct statements
    "want to die",
    "kill myself",
    "killing myself",
    "suicide",
    "suicidal",
    "end my life",
    "take my life",
    "commit suicide",
    "end it all",
    "end everything",
    "no reason to live",
    "don't want to live",
    "dont want to live",
    "not worth living",
    # indirect / comparative phrasing  ← covers "better to die like others did"
    "better to die",
    "better off dead",
    "better off dying",
    "wish i was dead",
    "wish i were dead",
    "rather be dead",
    "rather die",
    "like to die",
    "want it to end",
    "want everything to end",
    "thinking of ending",
    "plan to end",
    "hurt myself",
    "harm myself",
    "self harm",
    "self-harm",
]


class AnalyzeRequest(BaseModel):
    text: str


@app.post("/analyze")
def analyze(data: AnalyzeRequest):
    text = data.text.strip()
    text_lower = text.lower()

    #check first if phrase exist
    for phrase in HIGH_RISK_PHRASES:
        if phrase in text_lower:
            return {
                "expertise":  "Mental Health",
                "risk_level": "Critical",
                "confidence": 1.0
            }

    # 2. Predict expertise + confidence
    expertise_probs = expertise_model.predict_proba([text])[0]
    confidence      = float(max(expertise_probs))
    expertise       = expertise_model.predict([text])[0]

    
    word_count        = len(text.split())
    min_confidence    = 0.30 if word_count < 6 else 0.45

    if confidence < min_confidence:
        return {
            "success": False,
            "message": "Could not accurately determine the category. Please describe your situation in more detail."
        }

    # 4. Not an Incident guard
    if expertise == "Not an Incident":
        return {
            "success": False,
            "message": "No incident detected. Please describe a challenge or concern you are facing."
        }

    # 5. Predict risk only for real incidents
    risk = risk_model.predict([text])[0]

    return {
        "expertise":  str(expertise),
        "risk_level": str(risk),
        "confidence": confidence
    }