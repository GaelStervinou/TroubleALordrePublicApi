import {useEffect, useState} from "react";
import {useParams} from "react-router-dom";
import {getUserCompanies} from "../queries/companies.js";
import CardRow from "../components/molecules/CardRow.jsx";
import Button from "../components/atoms/Button.jsx";

export default function UserEstablishments() {
    const [establishments, setEstablishments] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const { userId } = useParams();
    const [hasMore, setHasMore] = useState(false);
    const [totalEstablishments, setTotalEstablishments] = useState(0);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        const fetchUserCompanies = async () => {
            const fetchedEstablishments = await getUserCompanies(userId, currentPage);
            setEstablishments(prevEstablishments => [...prevEstablishments, ...fetchedEstablishments["hydra:member"]]);
            setTotalEstablishments(fetchedEstablishments["hydra:totalItems"])
            if (fetchedEstablishments['hydra:view'] && fetchedEstablishments['hydra:view']['hydra:next']) {
                setHasMore(true);
            } else {
                setHasMore(false);
            }
            
        }
        fetchUserCompanies();
    }, [currentPage]);

    useEffect(() => {
        setIsLoading(false);
    }, [establishments]);

    return (
        <div className={''}>
            <p className={'text-secondary mb-14'}>{totalEstablishments} établissements</p>
            <section className={'item-paginate-container space-y-8'}>
                {establishments.map((establishment, index) => (
                    <CardRow
                        key={index}
                        id={establishment.id}
                        imagePath={`${import.meta.env.VITE_API_BASE_URL}${establishment.mainMedia.contentUrl}`}
                        title={establishment.name}
                        address={`${establishment?.address} ${establishment?.zipCode} ${establishment?.city}`}
                    />
                ))}
                {isLoading &&
                    <>
                        <div className="skeleton w-full h-44"></div>
                        <div className="skeleton w-full h-44"></div>
                        <div className="skeleton w-full h-44"></div>
                    </>
                }
                {hasMore &&
                    <Button
                        title={'Voir plus'}
                        onClick={() => {
                            setIsLoading(true);
                            setCurrentPage(currentPage + 1);
                        }}
                        hasBackground={false}
                        className={'!w-full !mt-6 !bg-on-surface rounded-xl !text-primary hover:!bg-accent-500 hover:!text-text'}/>
                }
            </section>
        </div>
    )
}