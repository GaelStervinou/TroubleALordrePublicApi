import {useEffect, useState} from "react";
import Button from "../atoms/Button.jsx";
import Search from "./Search.jsx";
import {useAuth} from "../../app/authContext.jsx";
import {useTheme} from "../../hooks/useTheme.jsx";
import { API_CREATE_COMPANY_ROUTE } from "../../utils/apiRoutes.js";


export default function Header() {
    const [scrollHeight, setScrollHeight] = useState(0);
    const {user, logout, goToMyProfile, isAdmin, isCompanyAdmin, retrieveUser} = useAuth();

    const {isSearchVisible} = useTheme();

    useEffect(() => {
        const handleScroll = () => {
            setScrollHeight(window.scrollY);
        };

        window.addEventListener('scroll', handleScroll);

        return () => {
            window.removeEventListener('scroll', handleScroll);
        };
    }, []);

    const handleLogout = () => {
        logout();

        window.location.href = '/';
    }

    return (
        <header className={`w-full fixed top-0 z-40 transition-all duration-300 shadow-inner ${scrollHeight > 50 ? 'h-20' : 'h-24'}`}>
            <div className="flex items-center p-0 h-full">
                <div className="absolute top-0 left-0 right-0 bg-gradient-to-b from-black to-transparent h-20" />

                <div className="absolute w-full">
                    <div className="mx-auto flex justify-between items-center px-8 lg:px-16 h-full">
                        <a href="/" className={'w-96'}>
                            <p className={'font-heading text-xl'}>TOLAP</p>
                        </a>
                        <div className={`h-12 transition-all duration-700 ${isSearchVisible ? 'opacity-1' : 'hidden opacity-0'}`}>
                            <Search />
                        </div>
                        <div className={'flex gap-4 w-96 justify-end items-center'}>
                            {isCompanyAdmin() ? (
                                <Button
                                   className={'max-lg:hidden'}
                                    title="Ajoutez votre établisement"
                                    href={API_CREATE_COMPANY_ROUTE}
                                />
                            ) : null }
                            { !user ? (
                                <a
                                    href="/login"
                                    className="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 whitespace-pre"
                                >
                                    Se connecter
                                </a>
                            ) : (
                                <div className="dropdown dropdown-end">
                                    <div tabIndex={0} role="button" className="btn btn-ghost btn-circle avatar">
                                        <div className="w-10 rounded-full bg-accent-200 text-accent-200">
                                            <img alt="profile picture" src={`${import.meta.env.VITE_API_BASE_URL}${retrieveUser().profilePicture?.contentUrl ?? '/media/default-profile-picture.jpeg'}`} className={'bg-accent-200'} />
                                        </div>
                                    </div>
                                    <ul tabIndex={0} className="mt-3 z-[1] p-2 shadow-lg menu menu-sm dropdown-content bg-surface rounded-box w-52">
                                        <li><a onClick={goToMyProfile}>Mon profil</a></li>
                                        {isAdmin() ? <li><a href="/admin/establishments">Back office</a></li> : null}
                                        <li><a onClick={handleLogout}>Se déconnecter</a></li>
                                    </ul>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </header>
    );
}