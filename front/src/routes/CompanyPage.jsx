import CardList from "../components/organisms/CardList.jsx";
import CardLiteList from "../components/organisms/CardLiteList.jsx";
import Carousel from "../components/atoms/Carousel.jsx";
import CardRoundedList from "../components/organisms/CardRoundedList.jsx";
import Button from "../components/atoms/Button.jsx";
import { IoIosPin } from "react-icons/io";
import CommentList from "../components/organisms/CommentList.jsx";
import Item from "../components/molecules/Item.jsx";
import {useEffect, useState} from "react";
import {useParams} from "react-router-dom";
import {getCompanies, getCompany} from "../queries/companies.js";
import ChipList from "../components/molecules/ChipList.jsx";
import Rating from "../components/atoms/Rating.jsx";

export default function CompanyPage() {
    const [company, setCompany] = useState(null);
    const [medias, setMedias] = useState([]);
    const {companySlug} = useParams();
    let [companies, setCompanies] = useState([]);
    let [isLoading, setIsLoading] = useState(true);


    useEffect(() => {
        const fetchCompany = async () => {
            const fetchedCompany = await getCompany(companySlug);
            fetchedCompany.rates = fetchedCompany.services.map((service) => {
                return service.rates;
            }).flat();
            setMedias([...fetchedCompany.medias, fetchedCompany.mainMedia]);
            setCompany(fetchedCompany);
            setIsLoading(false);
        };
        fetchCompany();

        const fetchRandomCompanies = async () => {
            const fetchedCompanies = await getCompanies();
            setCompanies(fetchedCompanies);
        }
        fetchRandomCompanies();
    }, [companySlug]);

    let citesList = [
        {
            "title": "Paris",
            "image": "https://cdn.paris.fr/paris/2019/10/01/huge-b3661d1d3cb578bc5752cc0d4237d592.jpg",
            "path": "/login"
        },
        {
            "title": "Marseille",
            "image": "https://a.cdn-hotels.com/gdcs/production76/d86/ded693e8-0d94-404e-8ae3-23739d6ec3bc.jpg",
            "path": "/login"
        },
        {
            "title": "Lyon",
            "image": "https://images.ctfassets.net/bth3mlrehms2/3FT2t7eUwluY8vEHRcQcBt/737ba261438c62dcc2bfc873d93690ed/France_Lyon_Quais_de_Sao__ne.jpg?w=2119&h=1414&fl=progressive&q=50&fm=jpg",
            "path": "/login"
        },
        {
            "title": "Bordeaux",
            "image": "https://www.novo-monde.com/app/uploads/2018/05/DSC07100.jpg",
            "path": "/login"
        }
    ];

    return (
        <>
            <div className={'mx-16 rounded-xl overflow-hidden max-sm:mx-0 mt-24'}>
                { isLoading ?
                    (<div className="carousel carousel-center w-full space-x-4 bg-transparent rounded-box h-96 ">
                        <div className="skeleton w-96 h-96"></div>
                        <div className="skeleton w-96 h-96"></div>
                        <div className="skeleton w-96 h-96"></div>
                    </div>) :
                    (<Carousel pictures={medias}/>)}
            </div>
            <div className={'w-full px-16 max-md:px-8'}>

                <section className={'w-full mt-8 flex gap-24 max-md:flex-col max-md:gap-3'}>
                    <section className={'w-2/3 space-y-32 max-md:w-full'}>
                        <header>
                            <h2 className={'text-3xl max-sm:text-xl font-medium mt-4 font-heading max-md:mt-0'}>
                                {isLoading ?
                                    (<div className="skeleton h-10 w-1/2"></div>)
                                    : (company?.name)
                                }
                            </h2>
                            <div className={'flex gap-3 text-lg my-2 mb-4 items-center max-md:text-base max-md:gap-1'}>
                                {isLoading ?
                                    (<div className="skeleton h-4 w-24"></div>)
                                    : (<>
                                        <IoIosPin />
                                        <p>
                                            {company?.address} {company?.zipCode} {company?.city}
                                        </p>
                                    </>)
                                }
                            </div>
                            {isLoading ?
                                (<div className={'space-y-3'}>
                                    <div className="skeleton h-6 w-14"></div>
                                </div>)
                                : (<ChipList chips={company?.categories}/>)
                            }
                            {isLoading ?
                                (<div className="skeleton mt-3 h-4 w-24"></div>) :
                                (<Rating isDisabled={true} value={company?.averageServicesRatesFromCustomer?.toFixed(1)}/>)
                            }
                            <div className={'mt-20'}>
                                {isLoading ?
                                    (<div className={'space-y-3'}>
                                        <div className="skeleton h-3 w-full"></div>
                                        <div className="skeleton h-3 w-full"></div>
                                        <div className="skeleton h-3 w-full"></div>
                                    </div>)
                                    : (company?.description)
                                }
                            </div>
                        </header>
                        <section>
                            <h2 className={'text-2xl max-sm:text-xl font-medium mt-4 font-heading max-md:text-base'}>Nos services</h2>
                            <p>
                                Choisissez votre service et prenez rendez-vous
                            </p>
                            <section className={'flex flex-col gap-6 mt-12'} id={'service-container'}>
                                {company?.services.map((service, index) => (
                                    <Item
                                        key={index}
                                        title={service.name}
                                        duration={service.duration}
                                        price={service.price}
                                        description={service.description}
                                        path={`/${companySlug}/order/${service.id}`}
                                    />
                                ))}
                            </section>
                        </section>
                        <section>
                            <h5 className={'mt-4 flex flex-col text-xl font-bold max-md:px-0 max-md:text-base max-md:mt-20'}>
                                <span className={'text-2xl font-heading max-md:text-xl'}>{company?.customerRates.length}</span>
                                Commentaires
                            </h5>
                            <CommentList items={company?.customerRates}/>
                        </section>
                        <section>
                            <h2 className={'text-2xl max-sm:text-xl font-medium mt-4 font-heading max-md:text-base'}>Notre équipe</h2>
                            <div className={'bg-surface py-12 mt-6 rounded-xl max-sm:py-8'}>
                                <CardRoundedList items={company?.companyActiveTroubleMakers }/>
                            </div>
                        </section>
                    </section>
                    <section className={'h-min w-1/3 space-y-10 max-md:w-[100%] max-md:mx-[-2rem] bg-accent-200 rounded-lg p-8 sticky top-28 mt-2 transition-all duration-700 max-sm:px-4 max-sm:fixed max-sm:p-3 max-sm:rounded-t-xl max-sm:rounded-b-none max-sm:left-8 max-sm:top-[100dvh] max-sm:pb-6 max-sm:z-50 max-sm:bg-on-surface max-sm:-translate-y-full max-sm:space-y-6'}>
                        <div className={'space-y-4'}>
                            <h2 className={'text-xl font-medium max-md:text-xl'}>
                                Choisissez votre service et prenez rendez-vous
                            </h2>
                            <div className="inline-block w-min bg-accent-500 rounded-md px-2 py-1 text-lg font-md text-secondary whitespace-pre">
                                Entre {company?.minimumServicePrice}€ et {company?.maximumServicePrice}€
                            </div>
                        </div>
                        <Button
                            href={'#service-container'}
                            title="Prendre rendez-vous"
                            hasBackground
                            className={'!w-full !bg-primary !text-background hover:!bg-secondary'}
                        />
                    </section>
                </section>
            </div>


            <section className={'w-full mt-28'}>
                <h2 className={'text-2xl px-16 font-medium mt-4 font-heading max-md:text-xl max-md:px-8'}>Poris lamis</h2>
                <p className={'px-16 max-md:px-8'}>
                    Lorem ipsum dolor sit amet, consectetur adipiscing
                </p>
                <CardList items={companies} />
            </section>

            <section className={'w-full mt-28 mb-36 max-sm:mb-64 px-16 max-md:px-8'}>
                <h2 className={'text-2xl font-medium mt-4 font-heading max-md:text-xl'}>Poris lamis</h2>
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing
                </p>
                <CardLiteList items={citesList} />
            </section>

        </>

    );
}