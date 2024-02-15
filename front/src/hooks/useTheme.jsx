import {createContext, useContext, useState} from "react";

const ThemeContext = createContext({
    isSearchVisible: true,
    setIsSearchVisible: () => {}
});

export function useTheme() {
    const {isSearchVisible, setIsSearchVisible} = useContext(ThemeContext);
    return {isSearchVisible, setIsSearchVisible};
}

export function ThemeContextProvider({children}) {
    const [isSearchVisible, setIsSearchVisible] = useState(true);

    return (
        <ThemeContext.Provider value={{
                isSearchVisible,
                setIsSearchVisible
            }}>
            {children}
        </ThemeContext.Provider>
    )
}