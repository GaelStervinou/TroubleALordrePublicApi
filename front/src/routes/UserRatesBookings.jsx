import {useEffect, useState} from "react";
import {getUserServicesRates} from "../queries/rates.js";
import Comment from "../components/molecules/Comment.jsx";
import {useParams} from "react-router-dom";
import Button from "../components/atoms/Button.jsx";
import {useTranslator} from "../app/translatorContext.jsx";

export default function UserRatesBookings() {
    const [ratesBookings, setRatesBookings] = useState([]);
    const { userId } = useParams();
    const [currentPage, setCurrentPage] = useState(1);
    const [hasMore, setHasMore] = useState(false);
    const [totalRates, setTotalRates] = useState(0);
    const [isLoading, setIsLoading] = useState(true);
    const {translate} = useTranslator();

    useEffect(() => {
        const fetchUserRates = async () => {
            const fetchedRatesBookings = await getUserServicesRates(userId, currentPage);
            setRatesBookings(prevRates => [...prevRates, ...fetchedRatesBookings["hydra:member"]]);
            setTotalRates(fetchedRatesBookings["hydra:totalItems"])
            if (fetchedRatesBookings['hydra:view'] && fetchedRatesBookings['hydra:view']['hydra:next']) {
                setHasMore(true);
            } else {
                setHasMore(false);
            }
        }
        fetchUserRates();
    }, [currentPage]);

    useEffect(() => {
        setIsLoading(false);
    }, [ratesBookings]);

    return (
        <div className={'-mt-20 max-sm:-mt-16'}>
            <p className={'text-secondary mb-14'}>{totalRates} avis</p>
            <section className={'comment-container-h space-y-8'}>
                {ratesBookings.map((rate, index) => (
                    <Comment
                        key={index}
                        authorImagePath={`${import.meta.env.VITE_API_BASE_URL}${rate.createdBy.profilePicture}`}
                        content={rate.content}
                        date={rate.createdAt}
                        rate={rate.value}
                        author={`${rate.createdBy.firstname} ${rate.createdBy.lastname}`}
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
                        title={translate("see-more")}
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