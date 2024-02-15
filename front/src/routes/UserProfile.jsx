import { useNavigate } from "react-router-dom";
import { useEffect } from "react";
import CustomerProfile from "../components/organisms/CustomerProfile.jsx";
import { useAuth } from "../app/authContext.jsx";

export default function UserProfile() {
    const { user, status, getMe, logout } = useAuth();
    const navigate = useNavigate();

    useEffect(() => {
        const checkLoggedInUser = async () => {
            try {
                await getMe();
            } catch (error) {
                navigate('/login');
            }
        };

        if (['failed', 'loggedOut'].includes(status)) {
            navigate('/login');
        } else {
            checkLoggedInUser();
        }
    }, [status]);

    const handleLogOut = async (event) => {
        event.preventDefault();
        logout();
        navigate('/login');
    };

    if (status === 'succeeded') {
        if (user.roles.includes('ROLE_ADMIN')) {
            return (
                <>
                    <h1>Admin Profile</h1>
                    <CustomerProfile userInformation={user} />
                    <p>Admin</p>
                </>
            )
        }
        if (user.roles.includes('ROLE_COMPANY_ADMIN')) {
            return (
                <>
                    <button className="btn btn-active" onClick={handleLogOut}>Log out</button>
                    <h1>Company Manager Profile</h1>
                    <CustomerProfile userInformation={user}/>
                </>
            )
        }
        return (
            <CustomerProfile userInformation={user} />
        )
    }
    if (status === 'loading') {
        return (
            <>
                <h1>User Profile</h1>
                <p>Loading...</p>
            </>
        )
    }
}
