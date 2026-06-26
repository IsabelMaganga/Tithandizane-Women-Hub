import pandas as pd
import joblib
from sklearn.pipeline import Pipeline
from sklearn.linear_model import LogisticRegression
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.model_selection import train_test_split
from sklearn.metrics import classification_report
import sklearn
import sys
import os

print(f"Python:      {sys.version}")
print(f"scikit-learn: {sklearn.__version__}")
print()

# ── Load dataset ──────────────────────────────────────────────────────────────
csv_path = "expertise_risk_classification_dataset_rebuilt.csv"
if not os.path.exists(csv_path):
    print(f"ERROR: {csv_path} not found in current directory.")
    print(f"Current directory: {os.getcwd()}")
    print("Files here:", os.listdir("."))
    sys.exit(1)

df = pd.read_csv(csv_path)
print(f"Dataset loaded: {df.shape[0]} rows")
print()
print("Expertise distribution:")
print(df['expertise'].value_counts().to_string())
print()
print("Risk distribution:")
print(df['risk'].value_counts().to_string())
print()

x        = df['text']
y_expert = df['expertise']
y_risk   = df['risk']

# ── Train expertise model ─────────────────────────────────────────────────────
print("=" * 50)
print("Training expertise model...")
x_tr, x_te, y_tr, y_te = train_test_split(
    x, y_expert, test_size=0.2, random_state=42, stratify=y_expert
)

expertise_model = Pipeline([
    ('tfidf',       TfidfVectorizer(ngram_range=(1, 2), max_features=50000)),
    ('classifier',  LogisticRegression(class_weight='balanced', max_iter=1000, C=1.0))
])
expertise_model.fit(x_tr, y_tr)

preds = expertise_model.predict(x_te)
print(classification_report(y_te, preds))

joblib.dump(expertise_model, 'expertise_model.pkl')
print("expertise_model.pkl saved")
print()

# ── Train risk model ──────────────────────────────────────────────────────────
print("=" * 50)
print("Training risk model...")
x_tr2, x_te2, y_tr2, y_te2 = train_test_split(
    x, y_risk, test_size=0.2, random_state=42, stratify=y_risk
)

risk_model = Pipeline([
    ('tfidf',       TfidfVectorizer(ngram_range=(1, 2), max_features=50000)),
    ('classifier',  LogisticRegression(class_weight='balanced', max_iter=1000, C=1.0))
])
risk_model.fit(x_tr2, y_tr2)

preds2 = risk_model.predict(x_te2)
print(classification_report(y_te2, preds2))

joblib.dump(risk_model, 'risk_model.pkl')
print("risk_model.pkl saved")
print()

# ── Sanity check ──────────────────────────────────────────────────────────────
print("=" * 50)
print("Sanity check:")
print()
tests = [
    "I can make it in life",
    "I can make it in life and be successful",
    "Hello how are you",
    "Testing 1 2 3",
    "I want to commit suicide I have no one",
    "I cannot afford my school fees and may be deregistered",
    "I feel so stressed and overwhelmed I cannot sleep",
    "I am struggling with my mental health every day",
]

print(f"{'Input':<50} {'Expertise':<22} {'Risk':<12} {'Conf'}")
print("-" * 100)
for t in tests:
    exp  = expertise_model.predict([t])[0]
    risk = risk_model.predict([t])[0]
    conf = max(expertise_model.predict_proba([t])[0])
    print(f"{t[:49]:<50} {exp:<22} {risk:<12} {conf:.2f}")

print()
print("Done. Both .pkl files are ready for FastAPI.")