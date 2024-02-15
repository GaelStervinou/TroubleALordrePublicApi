import {useEffect, useState} from "react";
import {getTroubleMakerReservations} from "../queries/users.js";
import {useParams} from "react-router-dom";
import Button from "../components/atoms/Button.jsx";
import CardLg from "../components/molecules/CardLg.jsx";

export default function UserBookings() {
    const [bookings, setBookings] = useState([]);
    const { userId } = useParams();
    const [currentPage, setCurrentPage] = useState(1);
    const [hasMore, setHasMore] = useState(false);
    const [totalBookings, setTotalBookings] = useState(0);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        const fetchUserReservations = async () => {
            const fetchedBookings = await getTroubleMakerReservations(userId, currentPage);
            setBookings(prevBookings => [...prevBookings, ...fetchedBookings["hydra:member"]]);
            setTotalBookings(fetchedBookings["hydra:totalItems"])
            if (fetchedBookings['hydra:view'] && fetchedBookings['hydra:view']['hydra:next']) {
                setHasMore(true);
            } else {
                setHasMore(false);
            }
        }
        fetchUserReservations();
    }, [currentPage]);

    useEffect(() => {
        setIsLoading(false);
    }, [bookings]);

    return (
        <div className={'-mt-20 max-sm:-mt-16'}>
            <p className={'text-secondary mb-14'}>{totalBookings} prestations avec</p>
            <section className={'item-paginate-container space-y-8'}>
                {bookings.map((booking, index) => (
                    <CardLg
                        key={index}
                        duration={booking.duration}
                        date={booking.date}
                        title={`${booking.service.name} avec ${booking.customer.firstname} ${booking.customer.lastname} chez ${booking.service.company.name}`}
                        path={`/reservations/${booking.id}`}
                        address={`${booking.service.company.address} ${booking.service.company.city} ${booking.service.company.zipCode}`}
                        imagePath={`${import.meta.env.VITE_API_BASE_URL}${booking.service.company.mainMedia.contentUrl}`}
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