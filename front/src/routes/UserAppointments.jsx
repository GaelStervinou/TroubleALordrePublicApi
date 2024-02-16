import {useParams} from "react-router-dom";
import {useEffect, useState} from "react";
import {getUserReservations} from "../queries/users.js";
import Button from "../components/atoms/Button.jsx";
import CardLg from "../components/molecules/CardLg.jsx";
import {useTranslator} from "../app/translatorContext.jsx";

export default function UserAppointments() {
    const [appointments, setAppointments] = useState([]);
    const { userId } = useParams();
    const [currentPage, setCurrentPage] = useState(1);
    const [hasMore, setHasMore] = useState(false);
    const [totalAppointments, setTotalAppointments] = useState(0);
    const [isLoading, setIsLoading] = useState(true);
    const {translate} = useTranslator();

    useEffect(() => {
        const fetchUserReservations = async () => {
            const fetchedAppointments = await getUserReservations(userId, currentPage);
            setAppointments(prevAppointments => [...prevAppointments, ...fetchedAppointments["hydra:member"]]);
            setTotalAppointments(fetchedAppointments["hydra:totalItems"])
            if (fetchedAppointments['hydra:view'] && fetchedAppointments['hydra:view']['hydra:next']) {
                setHasMore(true);
            } else {
                setHasMore(false);
            }
        }
        fetchUserReservations();
    }, [currentPage]);

    useEffect(() => {
        setIsLoading(false);
    }, [appointments]);

    return (
        <div className={'-mt-20 max-sm:-mt-16'}>
            <p className={'text-secondary mb-14'}>{totalAppointments} {translate("appointment-with")}</p>
            <section className={'item-paginate-container space-y-24'}>
                {appointments.map((appointment, index) => (
                    <CardLg
                        key={index}
                        duration={appointment.duration}
                        date={appointment.date}
                        title={`${appointment.service.name} ${translate("with")} ${appointment.troubleMaker.firstname} ${appointment.troubleMaker.lastname} ${translate("at")} ${appointment.service.company.name}`}
                        path={`/reservations/${appointment.id}`}
                        address={`${appointment.service.company.address} ${appointment.service.company.city} ${appointment.service.company.zipCode}`}
                        imagePath={`${import.meta.env.VITE_API_BASE_URL}${appointment.service.company.mainMedia.contentUrl}`}
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