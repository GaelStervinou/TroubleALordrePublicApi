import Chip from "../components/atoms/Chip.jsx"
import {Marker, Popup, useMap} from "react-leaflet";
import { MapContainer } from 'react-leaflet/MapContainer'
import { TileLayer } from 'react-leaflet/TileLayer'
import 'leaflet/dist/leaflet.css'
import {useSearch} from "../hooks/useSearch.jsx";
import CardRow from "../components/molecules/CardRow.jsx";
import MarkerClusterGroup from "react-leaflet-cluster";
import {FaMapPin} from "react-icons/fa";
import {useEffect, useState} from "react";
import {IoIosPin} from "react-icons/io";
import ChipList from "../components/molecules/ChipList.jsx";
import Rating from "../components/atoms/Rating.jsx";
import {AiOutlineRight} from "react-icons/ai";
import {Link} from "react-router-dom";
import {useTranslator} from "../app/translatorContext.jsx";

function SetViewOnClick() {
    const { searchCompaniesLatLng} = useSearch();
    const map = useMap();

    useEffect(() => {
        if (searchCompaniesLatLng) {
            map.setView(searchCompaniesLatLng ? [searchCompaniesLatLng[1], searchCompaniesLatLng[0]] : [48.857704218137656, 2.3478091021217695], map.getZoom());
        }
    }, [searchCompaniesLatLng] )

    return null;
}
export default function SearchPage() {
    const {searchCompaniesResults,searchCategories, searchCompaniesAddress, searchCompaniesCategory, searchCompaniesLatLng} = useSearch();
    const [selectedCompany, setSelectedCompany] = useState(null);
    const {translate} = useTranslator();

    return (
        <div className={'flex gap-8 pt-28 pb-14 w-full max-sm:!block h-[100dvh] max-sm:z-10 max-sm:py-0 -mb-28'}>
            <section className={'w-1/2 space-y-4 max-sm:w-full overflow-clip max-sm:absolute max-sm:top-[100dvh] max-sm:-translate-y-72 max-sm:bg-background z-20'}>
                {!selectedCompany &&
                    <div className="flex flex-col gap-4 max-sm:gap-3 pl-16 max-sm:p-8">
                        {searchCompaniesAddress &&
                            (<h1 className="text-lg font-medium">{translate("search-results-for")} {searchCompaniesAddress}</h1>)
                        }
                        <div className="flex gap-4 max-sm:gap-3">
                            {(searchCategories && searchCompaniesCategory) &&
                                searchCategories?.map((category, index) => (
                                    searchCompaniesCategory === category.id &&
                                        (<Chip key={index} title={category.name} />)
                                ))
                            }
                        </div>
                    </div>
                }
                <section className={'overflow-y-auto pl-16 pr-8 max-sm:p-8 pt-8 max-sm:overflow-visible max-h-full max-sm:h-fit scrollbar-hide flex flex-col gap-10 max-md:mb-28'}>
                    {selectedCompany ?
                        (
                            <Link to={`/${selectedCompany.id}`}>
                                <div className="ambilight hover:ambilight-on h-[200px] w-full max-sm:!h-[140px] -mt-8 mb-6">
                                    <img
                                        src={`${import.meta.env.VITE_API_BASE_URL}${selectedCompany.mainMedia.contentUrl}`}
                                        alt=""
                                        className="light w-full transition-all duration-700 rounded-md"/>
                                    <img
                                        src={`${import.meta.env.VITE_API_BASE_URL}${selectedCompany.mainMedia.contentUrl}`}
                                        alt="Image"
                                        className="w-full transition-all duration-700 z-10 rounded-md relative object-cover h-full"/>
                                </div>
                                <div className="text-text text-xl max-sm:text-lg hover:text-secondary font-bold w-full flex flex-col justify-between items-end">
                                    <header className={'flex flex-col gap-2 w-full'}>
                                        {selectedCompany.name}
                                        <div className={'flex gap-3 text-base font-normal mb-2 items-center max-md:gap-1'}>
                                            <IoIosPin className={'max-sm:hidden'}/>
                                            <p>{`${selectedCompany.address} ${selectedCompany.city} ${selectedCompany.zipCode}`}</p>
                                        </div>
                                        {selectedCompany.categories ?
                                            (<ChipList chips={selectedCompany.categories}/>) :
                                            null
                                        }
                                        {selectedCompany.averageServicesRatesFromCustomer ?
                                            (<Rating value={selectedCompany.averageServicesRatesFromCustomer}/>) :
                                            null
                                        }
                                    </header>

                                    <div className={'text-primary text-base flex items-center gap-2 max-md:text-sm'}>
                                        {translate("see-more")}
                                        <AiOutlineRight />
                                    </div>
                                </div>
                            </Link>
                        ):
                        (
                            searchCompaniesResults.length === 0 ?
                                (<p className="text-center">{translate("no-result")}</p>):
                                (
                                    searchCompaniesResults.map((company, index) => (
                                        <CardRow
                                            key={index}
                                            path={`/5501222a-cb29-11ee-92ef-0242ac150005`}
                                            title={company.name}
                                            imagePath={`${import.meta.env.VITE_API_BASE_URL}${company.mainMedia.contentUrl}`}
                                            rate={company.averageServicesRatesFromCustomer}
                                            categories={company.categories}
                                            address={`${company.address} ${company.city} ${company.zipCode}`}
                                        />
                                    ))
                                )
                        )

                    }
                </section>

            </section>
            <section className={'w-1/2 rounded-l-badge z-0 overflow-hidden max-sm:w-full max-sm:fixed max-sm:top-0 max-sm:left-0 max-sm:z-10 max-sm:rounded-none'}>
                <MapContainer
                    style={{height: '850px'}}
                    center={searchCompaniesLatLng ? [searchCompaniesLatLng[0], searchCompaniesLatLng[1]] : [48.857704218137656, 2.3478091021217695]}
                    zoomControl={false}
                    zoom={13}
                    scrollWheelZoom={true}
                >
                    <TileLayer
                        attribution='© <a href=\"https://www.mapbox.com/feedback/\">Mapbox</a> © <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a>'
                        url={`https://api.mapbox.com/styles/v1/${import.meta.env.VITE_MAPBOX_ACCOUNT}/${import.meta.env.VITE_MAPBOX_STYLE}/tiles/256/{z}/{x}/{y}@2x?access_token=${import.meta.env.VITE_MAPBOX_ACCESS}`}
                    />
                    <MarkerClusterGroup chunkedLoading>
                        {searchCompaniesResults.map((company, index) => (
                            <Marker
                                key={index}
                                eventHandlers={{
                                    click: () => {
                                        setSelectedCompany(company);
                                    },
                                }}
                                position={[company.lat, company.lng]}
                                title={company.name}
                                customIcon={<FaMapPin />}
                            ></Marker>
                        ))}
                    </MarkerClusterGroup>
                    <SetViewOnClick />
                </MapContainer>
            </section>
        </div>

    );
}