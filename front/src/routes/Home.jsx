import {Link} from "react-router-dom";
import videoBackground from "../assets/presentation-video.mp4";
import CardList from "../components/organisms/CardList.jsx";
import {AiOutlineSearch} from "react-icons/ai";
import CardLiteList from "../components/organisms/CardLiteList.jsx";
import SignInBanner from "../components/organisms/SignInBanner.jsx";
import {useEffect, useRef, useState} from "react";
import {useTheme} from "../hooks/useTheme.jsx";
import {getCompanies, getSearch} from "../queries/companies.js";
import {useSearch} from "../hooks/useSearch.jsx";
import {useTranslator} from "../app/translatorContext.jsx";

export default function Home() {
    const {language, translate} = useTranslator();
    console.log(language);
    const homeSearch = useRef(null);
    const {isSearchVisible, setIsSearchVisible} = useTheme();
    let [searchAddress, setSearchAddress] = useState('');
    let [isSearchAddressOpen, setIsSearchAddressOpen] = useState(false);
    let [searchResults, setSearchResults] = useState([]);
    let [searchCategory, setSearchCategory] = useState('');
    let [searchLatLong, setSearchLatLong] = useState([]);


    let [companies, setCompanies] = useState([]);

    const {
        searchCategories,
        setSearchCompaniesResults,
        setSearchCompaniesAddress,
        setSearchCompaniesCategory,
        setSearchCompaniesLatLng,
    } = useSearch();

    const handleCategoryChange = (e) => {
        setSearchCategory(e.target.value);
        setSearchCompaniesCategory(e.target.value);
    }

    const handleSearchChange = async (e) => {
        setSearchAddress(e.target.value);

        if (e.target.value.length > 3) {
            try {
                const response = await fetch(`https://api-adresse.data.gouv.fr/search/?q=${e.target.value}&autocomplete=1&limit=3`);
                const data = await response.json();
                setSearchResults(data.features);
            } catch (error) {
                console.error("Error", error);
            }
        }
    }

    const searchSubmit = async () => {
        if (searchCategory && searchLatLong) {
            const fetchSearch = await getSearch(searchLatLong[1], searchLatLong[0], searchCategory)
            setSearchCompaniesResults(fetchSearch);
            if (window.location.pathname !== '/search') {
                window.location.href = '/search';
            }
        }
    }

    useEffect(() => {
        const fetchRandomCompanies = async () => {
            const fetchedCompanies = await getCompanies();
            setCompanies(fetchedCompanies);
        }
        fetchRandomCompanies();

        const handleScroll = () => {
            if (homeSearch.current.getBoundingClientRect().top <= 0) {
                setIsSearchVisible(true);
            } else {
                setIsSearchVisible(false);
            }
        };

        window.addEventListener('scroll', handleScroll);
        return () => {
            setIsSearchVisible(true);
            window.removeEventListener('scroll', handleScroll);
        };
    }, []);

    return (
        <>
            <div className={'relative w-full h-[65dvh] max-sm:h-[55dvh] rounded shadow-lg flex mb-2 pb-4 max-sm:p-0 max-sm:flex-col'}>
                <video
                    autoPlay
                    loop
                    muted
                    src={videoBackground}
                    role="presentation"
                    className={'shadow-inner-right h-full w-full max-sm:h-full object-cover grayscale-[10%]'}
                    width={'1900'}
                    height={'600'}>
                    {translate("browser-video-unsupported")}
                </video>
                <div className="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent h-2/4 max-sm:h-[15dvh]">
                    <h1 className={'text-4xl text-text px-14 max-sm:px-8 ont-bold max-sm:text-xl'}>
                        <b className={'text-color-effect text-4xl font-heading max-sm:text-2xl'}>{translate("trouble-a-lordre-public")}</b><br/>
                    </h1>
                </div>

                <section className={`absolute bottom-0 w-full flex justify-center transition-all duration-700 translate-y-1/2 sticky-100 max-sm:hidden ${isSearchVisible ? 'opacity-0 scale-x-50' : 'opacity-1 scale-x-100'}`}>
                    <search ref={homeSearch} className={'flex mt-2 bg-surface rounded-xl overflow-hidden items-center h-full'}>
                        <select onChange={handleCategoryChange} defaultValue={translate("what-are-your-needs")} className="select bg-transparent w-64 px-3 py-1 placeholder-primary rounded-md text-primary text-base focus-visible:border-none">
                            <option disabled>{translate("what-are-your-needs")}</option>
                            {searchCategories.map((category, index) => (
                                <option value={category.id} key={index}>{category.name}</option>
                            ))}
                        </select>
                        <div>
                            <input
                                className={'focus-visible:border-none h-10 w-64 bg-transparent rounded-md text-text px-3 py-1 placeholder-primary text-base'}
                                type="text"
                                placeholder={translate("where")}
                                value={searchAddress}
                                onFocus={() => setIsSearchAddressOpen(true)}
                                onChange={handleSearchChange}
                            />
                            {searchResults.length > 0 && (
                                <div className={`shadow bg-surface p-2 absolute space-y-2 z-20 block rounded-xl ${isSearchAddressOpen ? 'block' : 'hidden'}`}>
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
                        <button onClick={searchSubmit} className={'h-20 text-surface text-base font-medium px-6 py-1 flex gap-4 items-center bg-primary transition-all duration-500 hover:bg-secondary'}>
                            <span className={'uppercase max-md:hidden'}>{translate("search")}</span>
                            <AiOutlineSearch className={'text-xl text-surface'}/>
                        </button>
                    </search>
                </section>
            </div>
            <section className={'w-full mt-28 max-sm:mt-20'}>
                <h2 className={'text-2xl px-16 font-medium mt-4 font-heading max-md:text-xl max-md:px-8'}>{translate("an-issue")}</h2>
                <p className={'px-16 max-md:px-8'}>
                    {translate("find-company-to-fit-your-needs")}
                </p>
                <CardList items={companies} />
            </section>

            <section className={'w-full mt-28 px-16 max-md:px-8 max-md:mt-16'}>
                <div className={'w-full p-16 bg-surface rounded-xl max-md:p-8'}>
                    <SignInBanner />
                </div>
            </section>

        </>

    );
}