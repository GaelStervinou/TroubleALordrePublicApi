import ProfileStat from "../atoms/ProfileStat.jsx";
import Rating from "../atoms/Rating.jsx";
import Chip from "../atoms/Chip.jsx";
import Button from "../atoms/Button.jsx";
import {getUser} from "../../queries/users.js";
import {NavLink, Outlet, useParams} from "react-router-dom";
import {useEffect, useState} from "react";
import {useAuth} from "../../app/authContext.jsx";
import { useNavigate } from "react-router-dom";

export default function CustomerProfile() {
    const [userInformation, setUserInformation] = useState([]);
    const [isTroubleMaker, setIsTroubleMaker] = useState(false);
    const [isMemberOfMyCompany, setIsMemberOfMyCompany] = useState(false);
    const [profilePicture, setProfilePicture] = useState('');
    const { userId } = useParams();


    const { isMyProfile, isCompanyAdmin, isMyCompany } = useAuth();
    const navigate = useNavigate();
        
    const updateProfileRedirection = () => {
        navigate(`/profile/${userId}/update`);
    }

    useEffect(() => {
        const fetchUser = async () => {
            const fetchedUser = await getUser(userId);
            fetchedUser.userAverageRatesValue = fetchedUser.userAverageRatesValue ? fetchedUser.userAverageRatesValue.toFixed(1) : null;

            setUserInformation(fetchedUser);

            if (fetchedUser?.profilePicture?.contentUrl){
                setProfilePicture(`${import.meta.env.VITE_API_BASE_URL}${fetchedUser.profilePicture.contentUrl}`)
            } else {
                setProfilePicture(`${import.meta.env.VITE_API_BASE_URL}/media/default-profile-picture.jpeg`)
            }


            if (fetchedUser.roles.includes('ROLE_TROUBLE_MAKER')) {
                setIsTroubleMaker(true);
            }
        }
        fetchUser();
    }, [userId]);

    useEffect(() => {
        if (isMyProfile(userId)) {
            navigate(`/profile/${userId}/calendar`);
        }
    }, [userId]);

    useEffect(() => {
        async function checkUserCompany() {
            if (isCompanyAdmin()) {
                const user = await getUser(userId);

                if (user.roles.includes('ROLE_TROUBLE_MAKER')) {
                    if (user.company) {
                        if (isMyCompany(user.company.id)) {
                            setIsMemberOfMyCompany(true);
                        }
                    }
                }
            }
        }

        checkUserCompany();
    }, [userId]);

    return (
        <div className={'mt-28 max-sm:mt-16 w-full px-16 max-sm:p-8'}>
            <section className={'w-full rounded-box p-12 max-sm:p-6 bg-surface mb-12'}>
                <div className={'flex justify-between gap-28 max-md:flex-col max-md:gap-8'}>
                    <header className={'flex gap-8 max-sm:gap-4'}>
                        <div className="story-outer-circle flex justify-center items-center w-32 h-32 max-sm:w-24 max-sm:h-24">
                            <img
                                className={'rounded-full h-[93%] w-[93%] bg-accent-200 object-cover border-surface border-[8px] text-accent-200'}
                                src={profilePicture }
                                alt={'profile picture'}
                            />
                        </div>
                        <div className={'space-y-3'}>
                            <h1 className={'text-2xl max-sm:text-lg whitespace-pre'}>{userInformation?.firstname} {userInformation?.lastname}</h1>
                            <div className={'flex items-center gap-4 ml-1'}>
                                {userInformation?.userAverageRatesValue ?
                                    (
                                        <>
                                            <p className={'font-bold'}>{userInformation?.userAverageRatesValue}</p>
                                            <Rating value={userInformation?.userAverageRatesValue} />
                                            <p className={'font-sm whitespace-pre -ml-2 text-on-surface'}>({userInformation?.ratesReceivedCount} avis)</p>
                                        </>
                                    ):
                                    null
                                }
                            </div>
                            <div className={'flex gap-1'}>
                                { userInformation?.roles?.map((role, index) => (
                                    (<Chip key={index} title={
                                        role === 'ROLE_USER' ? 'Client' :
                                        role === 'ROLE_COMPANY_ADMIN' ? 'PDG' :
                                        role === 'ROLE_ADMIN' ? 'Administrateur' :
                                        role === 'ROLE_TROUBLE_MAKER' ? 'Prestataire' : null
                                    } />)
                                ))}
                            </div>
                            <p className={'text-secondary'}>{
                                userInformation?.status === 1 ? 'Actif' :
                                userInformation?.status === 0 ? 'En attente' :
                                userInformation?.status === -1 ? 'Supprimé' : 
                                userInformation?.status === -2 ? 'Bloqué' : null
                            }</p>
                            { isMyProfile(userId) && 
                                <Button onClick={updateProfileRedirection} title="Modifier mon profil" hasBackground className={'mt-10 !w-full !bg-primary text-background hover:!bg-secondary'}/>
                            }
                        </div>
                    </header>
                    <section className={'flex w-full justify-around max-sm:gap-8'}>
                        <ProfileStat title={'Rendez-vous'} value={userInformation?.userReservationsAsCustomerCount} />
                        { isTroubleMaker && <ProfileStat title={'Prestations'} value={userInformation?.userReservationsAsTroubleMakerCount} /> }
                        <ProfileStat title={'Avis'} value={userInformation?.ratesReceivedCount} />
                    </section>
                </div>

            </section>
                <div role="tablist" className="tabs tabs-boxed bg-surface font-bold mb-12 max-sm:mb-8">
                    { (isMyProfile(userId) || isMemberOfMyCompany) &&
                        <NavLink to={`/profile/${userId}/calendar`} role="tab" className="tab text-primary">Réservations</NavLink>
                    }
                    {   isMemberOfMyCompany &&
                        <NavLink to={`/profile/${userId}/planning`} role="tab" className="tab text-primary">Horaires</NavLink>
                    }
                    {(isTroubleMaker && isMyProfile(userId)) ? <NavLink to={`/profile/${userId}/become-troublemaker`} role="tab" className="tab text-primary">Mes invitations</NavLink> : null}
                    <NavLink to={`/profile/${userId}/rates`} role="tab" className="tab text-primary">Avis</NavLink>
                    { (isCompanyAdmin() && isMyProfile(userId))  && <NavLink to={`/profile/${userId}/establishments`} role="tab" className="tab text-primary">Etablissements</NavLink> }
                </div>
            <Outlet/>
        </div>
    )
}