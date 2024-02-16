import React from 'react'
import ReactDOM from 'react-dom/client'
import './index.css'
import {
    createBrowserRouter,
    RouterProvider,
} from "react-router-dom";
import PageNotFound from "./components/errors/PageNotFound.jsx";
import Home from "./routes/Home.jsx";
import Login from "./routes/Login.jsx";
import Register from "./routes/Register.jsx";
import {LoadingPageError} from "./components/errors/LoadingPageError.jsx";
import Header from "./components/molecules/Header.jsx";
import Footer from "./components/molecules/Footer.jsx";
import CompanyPage from "./routes/CompanyPage.jsx";
import OrderPage from "./routes/OrderPage.jsx";
import ForgotPassword from './routes/ForgotPassword.jsx';
import SearchPage from "./routes/SearchPage.jsx";
import CompanyDashboardPage from "./routes/CompanyDashboardPage.jsx";
import { AuthProvider } from './app/authContext';
import {ThemeContextProvider} from "./hooks/useTheme.jsx";
import {OrderContextProvider} from "./hooks/useOrder.jsx";
import CustomerProfile from "./components/organisms/CustomerProfile.jsx";
import SubMenu from "./components/molecules/SubMenu.jsx";
import UserAppointments from "./routes/UserAppointments.jsx";
import UserBookings from "./routes/UserBookings.jsx";
import UserRatesAppointments from "./routes/UserRatesAppointments.jsx";
import UserRatesBookings from "./routes/UserRatesBookings.jsx";
import UserEstablishments from "./routes/UserEstablishments.jsx";
import ValidateAccount from './routes/ValidateAccount.jsx';
import AccountCreated from './routes/AccountCreated.jsx';
import ResetPassword from './routes/ResetPassword.jsx';
import UserUpdate from './routes/UserUpdate.jsx';
import BackOffice from "./routes/BackOffice.jsx";
import CompanyBackOffice from "./routes/CompanyBackOffice.jsx";
import CompanyRegister from './routes/CompanyRegister.jsx';
import ReservationPage from "./routes/ReservationPage.jsx";
import ServicesBackOffice from './routes/ServicesBackOffice.jsx';
import ServiceBackOfficeCreate from './routes/ServiceBackOfficeCreate.jsx';
import ServiceBackOfficeUpdate from './routes/ServiceBackOfficeUpdate.jsx';
import InvitationsBackOffice from './routes/InvitationsBackOffice.jsx';
import InvitationsBackOfficeCreate from './routes/InvitationsBackOfficeCreate.jsx';
import BackOfficeEstablishments from './routes/BackOfficeEstablishments.jsx';
import BackOfficeUsers from './routes/BackOfficeUsers.jsx';
import BackOfficeUsersUpdate from './routes/BackOfficeUsersUpdate.jsx';
import BackOfficeCategories from './routes/BackOfficeCategories.jsx';
import BackOfficeCategoriesCreate from './routes/BackOfficeCategoriesCreate.jsx';
import BackOfficeCategoriesUpdate from './routes/BackOfficeCategoriesUpdate.jsx';
import {SearchContextProvider} from "./hooks/useSearch.jsx";
import AvailabilitiesBackOffice from './routes/AvailabilitiesBackOffice.jsx';
import UnavailabilitiesBackOffice from './routes/UnavailabilitesBackOffice.jsx';
import UserAvailabilities from './routes/UserAvailabilities.jsx';
import UserUnavailabilities from './routes/UserUnavailabilities.jsx';
import UserAvailabilitiesCreate from './routes/UserAvailabilitiesCreate.jsx';
import UserUnavailabilitiesCreate from './routes/UserUnavailabilitiesCreate.jsx';
import ReservationRateCreate from './routes/ReservationRateCreate.jsx';
import UserInvitations from './routes/UserInvitations.jsx';
import BackOfficeUsersWaiting from './routes/BackOfficeUsersWaiting.jsx';
import {TranslatorProvider} from "./app/translatorContext.jsx";

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
        path: "/reservations/:reservationId/rate",
        element: <ReservationRateCreate/>,
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
                element: <SubMenu
                    path={[
                        {title: "Utilisateurs", path: "/admin/users/list"},
                        {title: "Futurs PDG", path: "/admin/users/waiting-for-company-validation"},
                    ]}
                />,
                children: [
                    {
                        path: "list",
                        element: <BackOfficeUsers/>,
                    },
                    {
                        path: "waiting-for-company-validation",
                        element: <BackOfficeUsersWaiting/>,
                    }
                ]
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
            },
            {
                path: "become-troublemaker",
                element: <UserInvitations/>,
            }
        ]
    },
    {
        path: "/profile/:userId/planning/availabilities/create",
        element: <UserAvailabilitiesCreate/>,
        errorElement: <LoadingPageError/>,
    },
    {
        path: "/profile/:userId/planning/unavailabilities/create",
        element: <UserUnavailabilitiesCreate/>,
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
        <TranslatorProvider>
            <SearchContextProvider>
                <ThemeContextProvider>
                    <Header/>
                    <main className="flex min-h-screen flex-col items-center pb-28 max-sm:pb-40">
                        <RouterProvider router={router}/>
                    </main>
                    <Footer/>
                </ThemeContextProvider>
            </SearchContextProvider>
        </TranslatorProvider>
    </AuthProvider>
)
