import { AiFillCaretUp, AiOutlineSearch } from "react-icons/ai";
import { useEffect, useState } from "react";
import { getCategories } from "../../queries/categories.js";
import {getSearch} from "../../queries/companies.js";
import {useSearch} from "../../hooks/useSearch.jsx";

export default function Search() {
    let [isSearchOpen, setIsSearchOpen] = useState(false);
    let [isSearchAddressOpen, setIsSearchAddressOpen] = useState(false);
    let [categories, setCategories] = useState([]);
    let [searchAddress, setSearchAddress] = useState('');
    let [searchResults, setSearchResults] = useState([]);
    let [searchLatLong, setSearchLatLong] = useState([]);
    let [searchCategory, setSearchCategory] = useState('');

    const {
        setSearchCompaniesResults,
        setSearchCompaniesAddress,
        setSearchCompaniesCategory,
        setSearchCompaniesLatLng,
        setSearchCategories
    } = useSearch()

    const toggleSearch = () => {
        setIsSearchOpen(!isSearchOpen);
    }

    const handleCategoryChange = (e) => {
        setSearchCategory(e.target.value);
        setSearchCompaniesCategory(e.target.value);
    }

    const handleSearchChange = async (e) => {
        setSearchAddress(e.target.value);

        if (e.target.value.length > 3) {
            try {
                const response = await fetch(`https://api-adresse.data.gouv.fr/search/?q=${e.target.value}&autocomplete=1`);
                const data = await response.json();
                setSearchResults(data.features);
            } catch (error) {
                console.error("Error", error);
            }
        }
    }

    useEffect(() => {
        const fetchCategories = async () => {
            const fetchedCategories = await getCategories();
            setCategories(fetchedCategories);
            setSearchCategories(fetchedCategories);
            setSearchCompaniesCategory(fetchedCategories);
        };
        fetchCategories();
    }, []);

    const searchSubmit = async () => {
        if (searchCategory && searchLatLong) {
            const fetchSearch = await getSearch(searchLatLong[1], searchLatLong[0], searchCategory)
            setSearchCompaniesResults(fetchSearch);
            if (window.location.pathname !== '/search') {
                window.location.href = '/search';
            }
        }
    }

    return (
        <>
            <search className={'flex bg-surface rounded-xl overflow-hidden items-center h-full max-sm:hidden'}>
                <select
                    defaultValue={'Quel service ?'}
                    className="select bg-transparent w-44 px-3 py-1 placeholder-primary rounded-md text-primary text-base focus-visible:border-none"
                    onChange={handleCategoryChange}
                >
                    <option disabled>Quel service ?</option>
                    {categories.map((category, index) => (
                        <option value={category.id} key={index}>{category.name}</option>
                    ))}
                </select>
                <div>
                    <input
                        className={'focus-visible:border-none h-10 w-44 bg-transparent rounded-md text-text px-3 py-1 placeholder-primary text-base'}
                        type="text"
                        placeholder="Où ça ?"
                        value={searchAddress}
                        onFocus={() => setIsSearchAddressOpen(true)}
                        onChange={handleSearchChange}
                    />
                    {searchResults.length > 0 && (
                        <div className={`shadow bg-surface p-2 absolute space-y-2 block rounded-xl ${isSearchAddressOpen ? 'block' : 'hidden'}`}>
                            {searchResults.map((result, index) => (
                                <div
                                    className={'hover:bg-on-surface p-2 rounded-xl cursor-pointer max w-full text-left'}
                                    key={index}
                                >
                                    <div onClick={() => {
                                            setSearchAddress(result.properties.label);
                                            setSearchCompaniesAddress(result.properties.label);
                                            setSearchLatLong(result.geometry.coordinates);
                                            setSearchCompaniesLatLng(result.geometry.coordinates);
                                            setIsSearchAddressOpen(false);
                                        }
                                    }>
                                        {result.properties.label}
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>

                <button
                    onClick={searchSubmit}
                    className={'h-20 text-surface text-base font-medium px-6 py-1 flex gap-4 items-center bg-primary transition-all duration-500 hover:bg-secondary'}>
                    <AiOutlineSearch className={'text-xl text-surface'}/>
                </button>
            </search>

            <div className={`max-sm:block hidden absolute space-y-1 w-full p-3 pb-8 px-4 left-0 top-[100dvh] -translate-y-full rounded-t-xl transition-all duration-700 ${isSearchOpen ? 'bg-surface' : 'bg-on-surface'}`}>
                <button onClick={toggleSearch} className={'w-full'}>
                    <div className={'flex items-center gap-2 text-primary font-medium justify-center'}>
                        <span>Rechercher</span>
                        <AiFillCaretUp className={`transition-all duration-700 ${isSearchOpen ? 'rotate-180' : 'rotate-0'}`}/>
                    </div>
                    <p className={'text-sm'}>
                        Vous cherchez un bar, un restaurant ou autre ?
                    </p>
               </button>
                <search className={`space-y-12 flex flex-col justify-end transition-all duration-700 ${isSearchOpen ? 'h-80 opacity-1' : 'opacity-0 h-0'}`}>
                    <select defaultValue={'Que cherchez-vous ?'} className="select bg-accent-200 w-full px-3 py-1 placeholder-primary rounded-md text-primary text-base focus-visible:border-none">
                        <option disabled >Que cherchez-vous ?</option>
                        {categories.map((category, index) => (
                            <option value={category.name} key={index}>{category.name}</option>
                        ))}
                    </select>
                </search>
            </div>
        </>
    )
}