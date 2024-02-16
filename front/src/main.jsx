import React from 'react'
import ReactDOM from 'react-dom/client'
import './index.css'
import {
    createBrowserRouter,
    RouterProvider,
} from "react-router-dom";
import PageNotFound from "./components/errors/PageNotFound.jsx";
import Home from "./ok/home.jsx";
import Login from "./ok/login.jsx";
import Register from "./ok/Register.jsx";
import {LoadingPageError} from "./components/errors/LoadingPageError.jsx";
import Header from "./components/molecules/Header.jsx";
import Footer from "./components/molecules/Footer.jsx";
import CompanyPage from "./ok/CompanyPage.jsx";
import OrderPage from "./ok/OrderPage.jsx";
import ForgotPassword from './ok/ForgotPassword.jsx';
import SearchPage from "./ok/SearchPage.jsx";
import CompanyDashboardPage from "./ok/CompanyDashboardPage.jsx";
import { AuthProvider } from './app/authContext';
import {ThemeContextProvider} from "./hooks/useTheme.jsx";
import {OrderContextProvider} from "./hooks/useOrder.jsx";
import CustomerProfile from "./components/organisms/CustomerProfile.jsx";
import SubMenu from "./components/molecules/SubMenu.jsx";
import UserAppointments from "./ok/UserAppointments.jsx";
import UserBookings from "./ok/UserBookings.jsx";
import UserRatesAppointments from "./ok/UserRatesAppointments.jsx";
import UserRatesBookings from "./ok/UserRatesBookings.jsx";
import UserEstablishments from "./ok/UserEstablishments.jsx";
import ValidateAccount from './ok/ValidateAccount.jsx';
import AccountCreated from './ok/AccountCreated.jsx';
import ResetPassword from './ok/ResetPassword.jsx';
import UserUpdate from './ok/UserUpdate.jsx';
import BackOffice from "./ok/BackOffice.jsx";
import CompanyBackOffice from "./ok/CompanyBackOffice.jsx";
import CompanyRegister from './ok/CompanyRegister.jsx';
import ReservationPage from "./ok/ReservationPage.jsx";
import ServicesBackOffice from './ok/ServicesBackOffice.jsx';
import ServiceBackOfficeCreate from './ok/ServiceBackOfficeCreate.jsx';
import ServiceBackOfficeUpdate from './ok/ServiceBackOfficeUpdate.jsx';
import InvitationsBackOffice from './ok/InvitationsBackOffice.jsx';
import InvitationsBackOfficeCreate from './ok/InvitationsBackOfficeCreate.jsx';
import BackOfficeEstablishments from './ok/BackOfficeEstablishments.jsx';
import BackOfficeUsers from './ok/BackOfficeUsers.jsx';
import BackOfficeUsersUpdate from './ok/BackOfficeUsersUpdate.jsx';
import BackOfficeCategories from './ok/BackOfficeCategories.jsx';
import BackOfficeCategoriesCreate from './ok/BackOfficeCategoriesCreate.jsx';
import BackOfficeCategoriesUpdate from './ok/BackOfficeCategoriesUpdate.jsx';
import {SearchContextProvider} from "./hooks/useSearch.jsx";
import AvailabilitiesBackOffice from './ok/AvailabilitiesBackOffice.jsx';
import UnavailabilitiesBackOffice from './ok/UnavailabilitesBackOffice.jsx';
import UserAvailabilities from './ok/UserAvailabilities.jsx';
import UserUnavailabilities from './ok/UserUnavailabilities.jsx';
import UserAvailabilitiesCreate from './ok/UserAvailabilitiesCreate.jsx';

const router = createBrowserRouter([
    {
        path: "/",
        element: <Home/>,
        errorElement: <LoadingPageError/>,
    },
    {
        path: "/error",
        element: <PageNotFound/>,
    },
    {
        path: "/login",
        element: <Login/>,
        errorElement: <LoadingPageError/>,
    },
    {
        path: "/register",
        element: <Register/>,
        errorElement: <LoadingPageError/>,
    },
    {
        path: "/company-register",
        element: <CompanyRegister/>,
        errorElement: <LoadingPageError/>,
    },
    {
        path: "/account-created",
        element: <AccountCreated/>,
        errorElement: <LoadingPageError/>,
    },
    {
        path: "/forgot-password",
        element: <ForgotPassword/>,
        errorElement: <LoadingPageError/>,
    },
    {
        path: "/reset-password/:token",
        element: <ResetPassword/>,
        errorElement: <LoadingPageError/>,
    },
    {
        path: "/validate-account/:token",
        element: <ValidateAccount/>,
        errorElement: <LoadingPageError/>,
    },
    {
        path: "/reservations/:reservationId",
        element: <ReservationPage/>,
        errorElement: <LoadingPageError/>,
    },
    {
        path: "/admin",
        element: <BackOffice/>,
        children: [
            {
                path: "establishments",
                element: <BackOfficeEstablishments/>,
            },
            {
                path: "users",
                element: <BackOfficeUsers/>,
            },
            {
                path: "categories",
                element: <BackOfficeCategories/>,
            }
        ]
    },
    {
        path: "/admin/users/:userId/update",
        element: <BackOfficeUsersUpdate/>,
    },
    {
        path: "/admin/categories/create",
        element: <BackOfficeCategoriesCreate/>,
    },
    {
        path: "/admin/categories/:categoryId/update",
        element: <BackOfficeCategoriesUpdate/>,
    },
    {
        path: "/:companySlug/admin",
        element: <CompanyBackOffice/>,
        children: [
            {
                path: "gestion",
                element: <SubMenu
                            path={[
                                {title: "Services", path: "/:companySlug/admin/gestion/services"},
                                {title: "Disponibilités", path: "/:companySlug/admin/gestion/availabilities"},
                                {title: "Indisponibilités", path: "/:companySlug/admin/gestion/unavailabilities"},
                            ]}
                        />,
                children: [
                    {
                        path: "services",
                        element: <ServicesBackOffice/>,
                    },
                    {
                        path: "availabilities",
                        element: <AvailabilitiesBackOffice/>,
                    },
                    {
                        path: "unavailabilities",
                        element: <UnavailabilitiesBackOffice/>,
                    }
                ]
            },
            {
                path: "invitations",
                element: <InvitationsBackOffice/>,
            }
        ]
    },
    {
        path: "/:companySlug/admin/gestion/services/create",
        element: <ServiceBackOfficeCreate/>,
        errorElement: <LoadingPageError/>,
    },
    {
        path: "/:companySlug/admin/gestion/services/:serviceId/update",
        element: <ServiceBackOfficeUpdate/>,
        errorElement: <LoadingPageError/>,
    },
    {
        path: "/:companySlug/admin/invitations/create",
        element: <InvitationsBackOfficeCreate/>,
        errorElement: <LoadingPageError/>,
    },
    {
        path: "/profile/:userId/",
        element: <CustomerProfile/>,
        children: [
            {
                path: "calendar/",
                element: <SubMenu
                            path={[
                                {title: "Prestataire", path: "/profile/:userId/calendar/appointments"},
                                {title: "Client", path: "/profile/:userId/calendar/bookings", troubleMakerOnly: true}
                            ]}
                        />,
                children: [
                    {
                        path: "appointments",
                        element: <UserAppointments/>,
                    },
                    {
                        path: "bookings",
                        element: <UserBookings/>,
                    }
                ]
            },
            {
                path: "planning/",
                element: <SubMenu
                            path={[
                                {title: "Disponibilités", path: "/profile/:userId/planning/availabilities"},
                                {title: "Indisponibilités", path: "/profile/:userId/planning/unavailabilities"},
                            ]}
                        />,
                children: [
                    {
                        path: "availabilities",
                        element: <UserAvailabilities/>,
                    },
                    {
                        path: "unavailabilities",
                        element: <UserUnavailabilities/>,
                    }
                ]
            },
            {
                path: "rates/",
                element: <SubMenu
                    path={[
                        {title: "Prestataire", path: "/profile/:userId/rates/appointments"},
                        {title: "Client", path: "/profile/:userId/rates/bookings", troubleMakerOnly: true}
                    ]}
                />,
                children: [
                    {
                        path: "appointments",
                        element: <UserRatesAppointments/>,
                    },
                    {
                        path: "bookings",
                        element: <UserRatesBookings/>,
                    }
                ]
            },
            {
                path: "establishments",
                element: <UserEstablishments/>,
            }
        ]
    },
    {
        path: "/profile/:userId/planning/availabilities/create",
        element: <UserAvailabilitiesCreate/>,
        errorElement: <LoadingPageError/>,
    },
    {
        path: "/profile/:userId/planning/availabilities/:availabilityId/update",
        element: <UserAvailabilities/>,
        errorElement: <LoadingPageError/>,
    },
    {
        path: "/profile/:userId/update",
        element: <UserUpdate/>,
        errorElement: <LoadingPageError/>,
    },
    {
        path: "/search",
        element: <SearchPage/>,
        errorElement: <LoadingPageError/>,
    },
    {
        path: "/:companySlug/order/:serviceId",
        element: <OrderContextProvider>
            <OrderPage/>
        </OrderContextProvider>,
        errorElement: <LoadingPageError/>,
    },
    {
        path: "/:companySlug",
        element: <CompanyPage/>,
        errorElement: <LoadingPageError/>,
    },
    {
        path: "/:companySlug/dashboard",
        element: <CompanyDashboardPage/>,
        errorElement: <LoadingPageError/>,
    },
]);

ReactDOM.createRoot(document.getElementById('root')).render(
    <AuthProvider>
        <SearchContextProvider>
            <ThemeContextProvider>
                <Header/>
                <main className="flex min-h-screen flex-col items-center pb-28 max-sm:pb-40">
                    <RouterProvider router={router}/>
                </main>
                <Footer/>
            </ThemeContextProvider>
        </SearchContextProvider>
    </AuthProvider>
)
