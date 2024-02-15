import {useEffect, useState} from "react";
import Rating from "../components/atoms/Rating.jsx";
import {Link, useParams} from "react-router-dom";
import {getReservation} from "../queries/reservations.js";
import CardRow from "../components/molecules/CardRow.jsx";
import {useAuth} from "../app/authContext.jsx";
import Chip from "../components/atoms/Chip.jsx";
import {useNavigate} from "react-router-dom";
import Comment from "../components/molecules/Comment.jsx";
import Button from "../components/atoms/Button.jsx";

export default function ReservationPage() {
    const [reservation, setReservation] = useState(null);
    const [user, setUser] = useState(null);
    const [isLoading, setIsLoading] = useState(true);
    const {reservationId} = useParams();

    const { retrieveUser } = useAuth();
    const navigate = useNavigate();

    useEffect(() => {
        async function getUser() {
            setUser(await retrieveUser());
        }
        getUser();
    }, []);

    useEffect(() => {
        const fetchReservation = async () => {
            const fetchedReservation = await getReservation(reservationId);
            
            const hour = new Date(fetchedReservation.date).toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit',
            }   );

            fetchedReservation.date = new Date(fetchedReservation.date).toLocaleDateString('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
            });

            fetchedReservation.hour = hour;

            if ((new Date(fetchedReservation.date) < new Date()) && fetchedReservation.status === 'active') {
                fetchedReservation.status = 'finished';
            }

            fetchedReservation.duration = new Date(fetchedReservation.duration * 1000).toISOString().substring(11, 16)

            setReservation(fetchedReservation);
            setIsLoading(false);
        };
        fetchReservation();

    }, [reservationId]);

    return (
        <div className={'mt-28 max-sm:mt-16 w-full px-16 max-sm:p-8'}>
            <section className={'w-full rounded-box p-12 max-sm:p-6 bg-surface mb-12'}>
                <div className={'flex justify-between gap-12 flex-col max-md:gap-8 w-full'}>
                    <header className={'flex gap-8 max-sm:gap-4 w-full'}>
                        <div className={'space-y-3 w-full'}>
                            <h1 className={'text-2xl max-sm:text-lg whitespace-normal'}>
                                {isLoading ?
                                    (<div className="skeleton w-32 h-8"></div>) :
                                    (`${reservation.service.name} chez ${reservation.service.company.name}`)
                                }
                            </h1>
                            <div className={'flex gap-1'}>
                                {isLoading ?
                                    (<div className="skeleton w-16 h-6"></div>) :
                                    ( <Chip title={reservation.status} />)
                                }
                            </div>
                            <div className={'w-full'}>
                                {isLoading ?
                                    (<>
                                        <div className="skeleton w-full h-3 mb-1"></div>
                                        <div className="skeleton w-full h-3 mb-1"></div>
                                        <div className="skeleton w-3/4 h-3 mb-1"></div>
                                    </>) :
                                    (reservation.service.description)
                                }
                            </div>
                        </div>
                    </header>
                    <section className={'flex w-full justify-around max-sm:gap-8'}>
                        <div className={'flex flex-col items-center text-text w-1/3'}>
                            {isLoading ?
                                (
                                    <>
                                        <div className="skeleton w-20 h-6 mb-2"></div>
                                        <div className="skeleton w-10 h-4"></div>
                                    </>
                                ) :
                                (
                                    <>
                                        <p className={'font-bold text-xl max-md:text-xl'}>{reservation?.date}</p>
                                        <label className={'text-lg max-sm:text-sm'}>Date</label>
                                    </>
                                )
                            }
                        </div>
                        <div className={'flex flex-col items-center text-text w-1/3'}>
                            {isLoading ?
                                (
                                    <>
                                        <div className="skeleton w-20 h-6 mb-2"></div>
                                        <div className="skeleton w-10 h-4"></div>
                                    </>
                                ) :
                                (
                                    <>
                                        <p className={'font-bold text-xl max-md:text-xl'}>{reservation?.hour}</p>
                                        <label className={'text-lg max-sm:text-sm'}>Heure</label>
                                    </>
                                )
                            }
                        </div>
                        <div className={'flex flex-col items-center text-text w-1/3'}>
                            {isLoading ?
                                (
                                    <>
                                        <div className="skeleton w-20 h-6 mb-2"></div>
                                        <div className="skeleton w-10 h-4"></div>
                                    </>
                                ) :
                                (
                                    <>
                                        <p className={'font-bold text-xl max-md:text-xl'}>{reservation?.duration}</p>
                                        <label className={'text-lg max-sm:text-sm'}>Durée</label>
                                    </>
                                )
                            }
                        </div>
                    </section>
                    { user?.id === reservation?.customer.id || user?.id === reservation?.troubleMaker.id ?
                        reservation?.rates.length < 2 ?
                            (reservation?.rates.filter(rate => rate?.createdBy.id === user?.id).length === 0) ?
                            <Button 
                                title={'Noter'}
                                onClick={() => navigate(`/reservations/${reservationId}/rate`)}
                                hasBackground 
                                className={'!w-full !bg-primary text-background hover:!bg-secondary mt-5'}/> 
                            : null
                        : null
                    : null
                    }
                </div>
            </section>
            <div className={'flex gap-8 max-sm:flex-col'}>
                { isLoading ?
                    (
                        <>
                            <div className="skeleton w-1/2 max-sm:w-full h-52"></div>
                            <div className="skeleton w-1/2 max-sm:w-full h-52"></div>
                        </>
                    ) :
                    (
                        <>
                            <section className={'rounded-box p-12 max-sm:p-6 bg-surface mb-12 max-sm:mb-4 w-1/2 max-sm:w-full'}>
                                <h5 className={'stat-title text-text font-medium'}>
                                    Le client
                                </h5>
                                <header className={'flex gap-8 max-sm:gap-4 mt-8'}>
                                    <Link to={`/profile/${reservation.customer.id}`}>
                                        <div className="story-outer-circle flex justify-center items-center  w-32 h-32 max-sm:w-24 max-sm:h-24 max-sm:!min-w-24">
                                            {isLoading ?
                                                (<div className="skeleton rounded-full w-32 h-32"></div>) :
                                                (<img
                                                    className={'rounded-full h-[93%] w-[93%] bg-accent-200 object-cover border-surface border-[8px]'}
                                                    src={`${import.meta.env.VITE_API_BASE_URL}${reservation.customer.profilePicture.contentUrl}`}
                                                    alt={reservation.service.name} />
                                                )
                                            }
                                        </div>
                                    </Link>
                                    <div className={'space-y-3'}>
                                        <h1 className={'text-2xl max-sm:text-lg whitespace-normal'}>
                                            {isLoading ?
                                                (<div className="skeleton w-32 h-8"></div>) :
                                                (`${reservation.customer.firstname} ${reservation.customer.lastname}`)
                                            }
                                        </h1>
                                        <div className={'flex gap-1'}>
                                            {isLoading ?
                                                (<div className="skeleton w-16 h-6"></div>) :
                                                ( reservation.customer.id === user?.id ?
                                                        (<Chip title={'Client'} />) :
                                                        (<Chip title={'Prestataire'} />)
                                                )
                                            }
                                        </div>

                                    </div>
                                </header>
                            </section>
                            <section className={'rounded-box p-12 max-sm:p-6 bg-surface mb-12 w-1/2 max-sm:w-full'}>
                                <h5 className={'stat-title text-text font-medium'}>
                                    Le prestataire
                                </h5>
                                <header className={'flex gap-8 max-sm:gap-4 mt-8'}>
                                    <Link to={`/profile/${reservation.troubleMaker.id}`}>
                                        <div className="story-outer-circle flex justify-center items-center  w-32 h-32 max-sm:w-24 max-sm:h-24 max-sm:!min-w-24">
                                            {isLoading ?
                                                (<div className="skeleton rounded-full w-32 h-32"></div>) :
                                                (<img
                                                        className={'rounded-full h-[93%] w-[93%] bg-accent-200 object-cover border-surface border-[8px]'}
                                                        src={`${import.meta.env.VITE_API_BASE_URL}${reservation.troubleMaker.profilePicture.contentUrl}`}
                                                        alt={reservation.service.name} />
                                                )
                                            }
                                        </div>
                                    </Link>
                                    <div className={'space-y-3'}>
                                        <h1 className={'text-2xl max-sm:text-lg whitespace-normal'}>
                                            {isLoading ?
                                                (<div className="skeleton w-32 h-8"></div>) :
                                                (`${reservation.troubleMaker.firstname} ${reservation.troubleMaker.lastname}`)
                                            }
                                        </h1>
                                        <div className={'flex gap-1'}>
                                            {isLoading ?
                                                (<div className="skeleton w-16 h-6"></div>) :
                                                ( reservation.troubleMaker.id === user?.id ?
                                                        (<Chip title={'Client'} />) :
                                                        (<Chip title={'Prestataire'} />)
                                                )
                                            }
                                        </div>

                                    </div>
                                </header>
                            </section>
                        </>
                    )
                }
            </div>
            { isLoading ?
                (<div className="skeleton mt-8 w-40 h-40"></div>) :
                (
                    <CardRow
                        path={`/${reservation.service.company.id}`}
                        title={reservation.service.company.name}
                        imagePath={`${import.meta.env.VITE_API_BASE_URL}${reservation.service.company.mainMedia.contentUrl}`}
                        address={`${reservation.service.company.address} ${reservation.service.company.city} ${reservation.service.company.zipCode}`}
                    />
                )
            }
            <div className={'flex gap-8 max-sm:flex-col mt-10'}>
                { isLoading ?
                    (
                        <>
                            <div className="skeleton w-1/2 max-sm:w-full h-52"></div>
                            <div className="skeleton w-1/2 max-sm:w-full h-52"></div>
                        </>
                    ) :
                    (
                        <>
                            {reservation.rates.map((rate, index) => (
                                <Comment
                                    key={index}
                                    content={rate.content}
                                    authorImagePath={`${import.meta.env.VITE_API_BASE_URL}${rate.createdBy.profilePicture.contentUrl}`}
                                    rate={rate.value}
                                    isFullWidth={true}
                                    author={`${rate.createdBy.firstname} ${rate.createdBy.lastname}`}
                                />
                            ))}

                        </>
                    )
                }
            </div>
        </div>
    );
}