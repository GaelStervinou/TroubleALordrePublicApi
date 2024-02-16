import Button from "../components/atoms/Button.jsx";
import Step from "../components/atoms/Step.jsx";
import AccordionItem from "../components/atoms/AccordionItem.jsx";
import Chip from "../components/atoms/Chip.jsx";
import {useOrder} from "../hooks/useOrder.jsx";
import {useEffect, useState} from "react";
import {getCompanyUsers} from "../queries/companies.js";
import {useParams, useNavigate} from "react-router-dom";
import CardRounded from "../components/molecules/CardRounded.jsx";
import {getService} from "../queries/services.js";
import {getUserServicePlanning} from "../queries/users.js";
import {useAuth} from "../app/authContext.jsx";
import {createReservation} from "../queries/reservations.js";
import {useTranslator} from "../app/translatorContext.jsx";

export default function OrderPage() {
    const {companySlug, serviceId} = useParams();
    const [nextStepAvailable, setNextStepAvailable] = useState(false);
    const [currentPlanningPage, setCurrentPlanningPage] = useState(1);
    const [collaboratorList, setCollaboratorList] = useState([]);
    const [providerCalendar, setProviderCalendar] = useState([]);
    const [error, setError] = useState(null);
    const {translate, getLanguageForDate} = useTranslator();

    const {isLoggedIn, retrieveUser} = useAuth();
    const navigate = useNavigate();

    useEffect(() => {
        if (!isLoggedIn()) {
            navigate('/login');
        }
    }, []);


    let {
        nextStepName, setNextStepName,
        appointmentDate, setAppointmentDate,
        currentStep, setCurrentStep,
        provider, setProvider,
        service, setService
    } = useOrder();

    const handleNextStep = async () => {
        if (currentStep === 'Prestataire') {
            setCurrentStep('Date');
            setNextStepName('Confirmation');
        } else if (currentStep === 'Date') {
            setCurrentStep('Confirmation');

            const localDate = new Date(appointmentDate);
            const offset = localDate.getTimezoneOffset();

            // Ajustez la date en fonction du décalage du fuseau horaire
            const adjustedDate = new Date(localDate.getTime() - offset * 60 * 1000);


            const reservation = {
                description: service.description,
                date: adjustedDate.toISOString(),
                duration: service.duration,
                price: service.price,
                service: service['@id'],
                troubleMaker: provider['@id']
            }


            const response = await createReservation(reservation);

            if (response.status === 201) {
                setCurrentStep('Confirmation');
                // navigate(`/profile/${retrieveUser().id}/calendar`);
            } else {
                setCurrentStep('Confirmation');
                setError('Une erreur est survenue lors de la création de votre rendez-vous, veuillez réessayer ultérieurement')
            }
        }
    }    
    
    const handleRedirect = () => {
        if (currentStep === 'Confirmation') {
            navigate(`/profile/${retrieveUser().id}/calendar/appointments`);
        }
    }

    useEffect(() => {
        setNextStepAvailable(false);
    }, [currentStep]);

    useEffect(() => {
        const fetchService = async () => {
            const fetchedService = await getService(serviceId);
            setService(fetchedService);
        }
        fetchService();
    }, [serviceId]);

    useEffect(() => {
        const fetchCompanyUsers = async () => {
            const fetchedCompanyUsers = await getCompanyUsers(companySlug);
            setCollaboratorList(fetchedCompanyUsers);
        }
        fetchCompanyUsers();
    }, [companySlug]);

    useEffect(() => {
        if (provider) {
            setNextStepAvailable(true);
            const fetchUserServicePlanning = async () => {
                const fetchedServicePlanning = await getUserServicePlanning(provider.id, serviceId, currentPlanningPage);
                const formattedPlanning = fetchedServicePlanning.map((planning) => {
                        let shifts = [];
                        for (const value in planning.shifts) {
                            shifts.push(planning.shifts[value].startTime);
                        }
                        return {
                            date: planning.date,
                            shifts: shifts
                        }

                    }
                );
                setProviderCalendar(formattedPlanning);
            }
            fetchUserServicePlanning();
        }

    }, [provider]);

    useEffect(() => {
        const fetchUserServicePlanning = async () => {
            const fetchedServicePlanning = await getUserServicePlanning(provider.id, serviceId, currentPlanningPage);
            const formattedPlanning = fetchedServicePlanning.map(planning => ({
                date: planning.date,
                shifts: planning.shifts.map(shift => shift.startTime)
            }));
            setProviderCalendar(prevPlanning => [...prevPlanning, ...formattedPlanning]);
        }

        if (provider) {
            fetchUserServicePlanning();
        }

    }, [currentPlanningPage]);

    return (
        <>
            <div className={'w-full px-16 max-md:px-8 mt-24 max-sm:mt-10 pb-20'}>
                <section className={'w-full mt-16 flex gap-24 max-md:flex-col max-md:gap-3 relative'}>
                    <section className={'w-2/3 max-w-2/3 space-y-24 max-md:w-full max-sm:space-y-14'}>
                        <Step steps={['Service', 'Prestataire', 'Date', 'Confirmation']} activeStep={currentStep}/>
                        <section className={'space-y-10'}>
                            {currentStep === 'Prestataire' ? (
                                <section className={'space-y-10 max-sm:space-y-8'}>
                                    <h2 className={'text-xl font-medium'}>
                                        Choisissez un prestataire
                                    </h2>
                                    <div className={'bg-surface py-12 mt-6 rounded-xl max-sm:py-6'}>
                                        <div className="overflow-x-scroll flex gap-8 w-full max-w-full scrollbar-hide px-8 max-sm:px-6 max-sm:gap-6">
                                            {collaboratorList?.map((item, index) => (
                                                <CardRounded key={index} onClick={() => {setProvider(item)}} imagePath={`${import.meta.env.VITE_API_BASE_URL}${item.profilePicture.contentUrl ?? '/'}`} title={`${item.firstname} ${item.lastname}`} />
                                            ))}
                                        </div>
                                    </div>
                                </section>
                            ) : currentStep === 'Date' ? (
                                <section className={'space-y-10'}>
                                    <h2 className={'text-xl font-medium'}>
                                        Choisissez une date
                                    </h2>
                                    <div className={'space-y-2'}>
                                        {providerCalendar.map((day, dayIndex) => 
                                        (
                                            <AccordionItem
                                                key={dayIndex}
                                                title={new Date(day.date).toLocaleDateString('fr-FR', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'})}
                                                checked={'checked'}
                                                accordionId={'providerCalendar'}>
                                                <div className={'flex gap-1 flex-wrap'}>
                                                    {day.shifts.map((slot, slotIndex) => (
                                                        <Chip
                                                            key={slotIndex}
                                                            title={slot}
                                                            onClick={() => {
                                                                setAppointmentDate(`${day.date} ${slot}`)
                                                                setNextStepAvailable(true)
                                                            }}
                                                        />
                                                    ))}
                                                </div>
                                            </AccordionItem>
                                        ))}
                                        <Button
                                            title={'Voir plus de dates'}
                                            onClick={() => {
                                                setCurrentPlanningPage(currentPlanningPage + 1);
                                            }}
                                            hasBackground={false}
                                            className={'!w-full !mt-6 !bg-on-surface rounded-xl !text-primary hover:!bg-accent-500 hover:!text-text'}
                                            />
                                    </div>
                                </section>
                            ) : (
                                <section className={'space-y-10'}>
                                    <h2 className={'text-xl font-medium'}>
                                        Confirmation
                                    </h2>
                                    {error ? (
                                        <div className={'bg-error p-4 rounded-lg'}>
                                            {error}
                                        </div>
                                    ) : (
                                        <section className={'space-y-2'}>
                                            <h1 className={'text-2xl font-medium'}>
                                                {translate("thank-for-trust")}
                                            </h1>
                                            <h3 className={'text-lg font-medium text-secondary'}>
                                                {translate("your-appointment")} {provider?.firstname} {provider?.lastname} {translate("at")} {service?.company.name}  {new Date(appointmentDate).toLocaleDateString(getLanguageForDate(), {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric'})}
                                            </h3>
                                        </section>
                                    )}
                                </section>
                            )}
                        </section>
                    </section>
                    <section className={'h-min w-1/3 space-y-10 max-md:w-[100%] max-md:mx-[-2rem] bg-accent-200 rounded-lg p-8 sticky top-28 mt-2 transition-all duration-700 max-sm:px-4 max-sm:fixed max-sm:p-3 max-sm:rounded-t-xl max-sm:rounded-b-none max-sm:left-8 max-sm:top-[100dvh] max-sm:pb-6 max-sm:z-50 max-sm:bg-on-surface max-sm:-translate-y-full max-sm:space-y-4'}>
                        <div className={'space-y-4 max-sm:space-y-1'}>
                            <h2 className={'text-xl font-medium max-md:text-xl max-sm:hidden'}>
                                {translate("your-appointment-at")} {service?.company.name}
                            </h2>
                            { appointmentDate ? (
                                <div className={'text-sm font-md text-secondary'}>{new Date(appointmentDate).toLocaleDateString(getLanguageForDate(), {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric'})}</div>
                            ) : null}
                            <hr className={'max-sm:hidden'}/>
                            <div className={'flex justify-between font-md text-lg'}>
                                <p>
                                    {service?.name}
                                    { provider ? (
                                        ` avec ${provider.firstname} ${provider.lastname}`
                                    ) : null}
                                </p>
                                <span>
                                    {service?.price}€
                                </span>
                            </div>
                        </div>
                        { currentStep === 'Confirmation' ? (
                            <Button
                                onClick={handleRedirect}
                                title={translate("see-my-calendar")}
                                hasBackground
                                className={'!w-full !bg-primary !text-background hover:!bg-secondary'}/>
                        ) : (
                            <Button
                                onClick={handleNextStep}
                                title={ nextStepName === 'Confirmation' ? translate("confirm") : translate("choose-a") + ' ' + nextStepName}
                                hasBackground
                                className={'!w-full !bg-primary !text-background hover:!bg-secondary disabled:!bg-on-surface'}
                                disabled={!nextStepAvailable}
                            />
                        )}
                    </section>
                </section>
            </div>
        </>

    );
}