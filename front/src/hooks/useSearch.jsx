import {createContext, useContext, useState} from "react";

const SearchContext = createContext({
    searchCompaniesResults: [],
    setSearchCompaniesResults: () => {},
    searchCompaniesAddress: null,
    setSearchCompaniesAddress: () => {},
    searchCompaniesLatLng: [48.857704218137656, 2.3478091021217695],
    setSearchCompaniesLatLng: () => {},
    searchCompaniesCategory: null,
    setSearchCompaniesCategory: () => {},
    searchCategories: [],
    setSearchCategories: () => {},
});

export function useSearch() {
    const {
        searchCompaniesResults,
        setSearchCompaniesResults,
        searchCompaniesAddress,
        setSearchCompaniesAddress,
        searchCompaniesLatLng,
        setSearchCompaniesLatLng,
        searchCompaniesCategory,
        setSearchCompaniesCategory,
        searchCategories,
        setSearchCategories
    } = useContext(SearchContext);
    return {
        searchCompaniesResults,
        setSearchCompaniesResults,
        searchCompaniesAddress,
        setSearchCompaniesAddress,
        searchCompaniesLatLng,
        setSearchCompaniesLatLng,
        searchCompaniesCategory,
        setSearchCompaniesCategory,
        searchCategories,
        setSearchCategories
    };
}

export function SearchContextProvider({children}) {
    let [searchCompaniesResults, setSearchCompaniesResults] = useState([]);
    let [searchCompaniesAddress, setSearchCompaniesAddress] = useState(null);
    let [searchCompaniesLatLng, setSearchCompaniesLatLng] = useState([48.857704218137656, 2.3478091021217695]);
    let [searchCompaniesCategory, setSearchCompaniesCategory] = useState(null);
    let [searchCategories, setSearchCategories] = useState([]);

    return (
        <SearchContext.Provider value={{
                searchCompaniesResults,
                setSearchCompaniesResults,
                searchCompaniesAddress,
                setSearchCompaniesAddress,
                searchCompaniesLatLng,
                setSearchCompaniesLatLng,
                searchCompaniesCategory,
                setSearchCompaniesCategory,
                searchCategories,
                setSearchCategories
            }}>
            {children}
        </SearchContext.Provider>
    )
}