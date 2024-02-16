import {createContext, useContext, useState} from "react";
import {getLanguage} from "../utils/localStorage.js";
import {frenchTranslation} from "../translator/fr.js";
import {englishTranslation} from "../translator/en.js";


const TranslatorContext = createContext();
export const TranslatorProvider = ({ children }) => {
    const [language, setLanguage] = useState(getLanguage() ?? 'fr');
    const changeLanguage = (lang) => {
        setLanguage(lang);
    }

    const getLanguageForDate = () => {
        return language+"-"+language.toUpperCase();
    }

    const translate = (key) => {
        if (language === 'fr') {
            const translation = frenchTranslation[key];
            if (translation === undefined) {
                return englishTranslation[key]
            }
            return translation;
        } else if (language === 'en') {
            const translation = englishTranslation[key];
            if (translation === undefined) {
                return frenchTranslation[key]
            }
            return translation;
        } else {
            return "Couldn't find translation in this language : " + language;
        }
    }

    const value = {language, changeLanguage, translate, getLanguageForDate}

    return (
        <TranslatorContext.Provider value={value}>
            {children}
        </TranslatorContext.Provider>
    )
}

export const useTranslator= () => {
    return useContext(TranslatorContext);
}