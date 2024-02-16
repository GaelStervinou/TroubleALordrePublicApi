import {jwtDecode} from "jwt-decode";

const LS_KEY_PREFIX = 'talop';
const LS_TOKEN_KEY = LS_KEY_PREFIX + 'Token';
const LS_REFRESH_TOKEN_KEY = LS_KEY_PREFIX + 'RefreshToken';
const LS_USER_INFORMATION_KEY = LS_KEY_PREFIX + 'UserInformation';
const LS_LANGUAGE = LS_KEY_PREFIX + 'Language';

export function localStorageStoreItem(name, item, isObject = false) {
    if (isObject) {
        localStorage.setItem(name, JSON.stringify(item));
        return;
    }
    localStorage.setItem(name, item);
}

export function localStorageGetItem(name, isObject = false) {
    if (!isObject) {
        return localStorage.getItem(name);
    }
    return JSON.parse(localStorage.getItem(name));
}

export function localStorageRemoveItem(name) {
    localStorage.removeItem(name);
}

export function storeCredentials(token, refreshToken) {
    localStorageStoreItem(LS_TOKEN_KEY, token);
    localStorageStoreItem(LS_USER_INFORMATION_KEY, jwtDecode(token), true);
    localStorageStoreItem(LS_REFRESH_TOKEN_KEY, refreshToken);
}

export function getAccessToken() {
    return localStorageGetItem(LS_TOKEN_KEY);
}

export function getRefreshToken() {
    return localStorageGetItem(LS_REFRESH_TOKEN_KEY);
}

export function eraseAccessToken() {
    localStorageRemoveItem(LS_TOKEN_KEY);
}

export function eraseCredentials() {
    localStorageRemoveItem('user');
    localStorageRemoveItem(LS_TOKEN_KEY);
    localStorageRemoveItem(LS_USER_INFORMATION_KEY);
    localStorageRemoveItem(LS_REFRESH_TOKEN_KEY);
}

export function storeLanguage(language) {
    localStorageStoreItem(LS_LANGUAGE, language);
}

export function getLanguage() {
    return localStorageGetItem(LS_LANGUAGE);
}