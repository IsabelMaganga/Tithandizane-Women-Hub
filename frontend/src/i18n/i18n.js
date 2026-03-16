import i18n from "i18next";
import { initReactI18next } from "react-i18next";

// Load JSON files (one per namespace per language)
import enCommon from "./locales/en/common.json";
import enAuth from "./locales/en/auth.json";
import enSettings from "./locales/en/settings.json";

import chCommon from "./locales/ch/common.json";
import chAuth from "./locales/ch/auth.json";
import chSettings from "./locales/ch/settings.json";

import tuCommon from "./locales/tu/common.json";
import tuAuth from "./locales/tu/auth.json";
import tuSettings from "./locales/tu/settings.json";

// Initialize i18next
i18n
  .use(initReactI18next)
  .init({
    compatibilityJSON: "v3",
    lng: "en", // default language
    fallbackLng: "en",

    // Namespaces
    ns: ["common", "auth", "settings"],
    defaultNS: "common",

    // Resources
    resources: {
      en: { common: enCommon, auth: enAuth, settings: enSettings },
      ch: { common: chCommon, auth: chAuth, settings: chSettings },
      tu: { common: tuCommon, auth: tuAuth, settings: tuSettings },
    },

    interpolation: { escapeValue: false },
  });

export default i18n;

export const changeLanguage = (lang: string) => {
  i18n.changeLanguage(lang);
};