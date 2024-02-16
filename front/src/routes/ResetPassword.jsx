import { useState } from "react";
import { useNavigate } from "react-router-dom";
import TextInput from "../components/atoms/TextInput.jsx";
import WarningAlert from "../components/atoms/WarningAlert.jsx";
import Button from "../components/atoms/Button.jsx";
import SetUpInstance from '../utils/axios.js';
import { API_RESET_PASSWORD_ROUTE } from '../utils/apiRoutes.js';
import {useTranslator} from "../app/translatorContext.jsx";

export default function ResetPassword() {
    const [password, setPassword] = useState("");
    const [verifyPassword, setVerifyPassword] = useState("");
    const [areInvalidCredentials, setAreInvalidCredentials] = useState(false);
    const [errorDisplay, setErrorDisplay] = useState("");
    const http = SetUpInstance();
    const navigate = useNavigate();

    const handlePasswordChange = (event) => {
        setPassword(event.target.value);
    }

    const handleVerifyPasswordChange = (event) => {
        setVerifyPassword(event.target.value);
    }


    const isPasswordValid = () => {
        const passwordRegex = /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/;

        return passwordRegex.test(password);
    };

    const {translate} = useTranslator();

    const handleSubmit = async (event) => {
        event.preventDefault();
        try {
            if (password !== verifyPassword || !isPasswordValid()) {
                throw new Error('Invalid password or mismatch');
            }

            const token = window.location.pathname.split('/')[2];
            const userCredentials = {
                plainPassword: password,
                verifyPassword: verifyPassword,
            };
            await http.patch(API_RESET_PASSWORD_ROUTE + `/${token}`, userCredentials);
            navigate('/login');
        } catch (error) {
            setAreInvalidCredentials(true);
            setErrorDisplay(error);
        }
    }

    return (
        <div className="mt-64 bg-background max-sm:mt-28">
            <div className="hero-content flex-col lg:flex-row-reverse">
                <div className="text-center lg:text-left">
                    <h1 className={'text-4xl text-text px-14 max-sm:px-8 ont-bold max-sm:text-xl'}>
                        <b className={'text-color-effect text-4xl font-heading max-sm:text-2xl'}> Baristos</b><br/>
                        <span className={'text-4xl max-sm:text-2xl'}>Reset your password</span>
                    </h1>
                    <p className={'text-text max-sm:text-sm px-14 max-sm:px-8'}>
                        {translate("new-password-and-confirm-it")}
                    </p>
                </div>
                <div className="card shrink-0 w-full max-w-sm shadow-2xl">
                    <form className="card-body" onSubmit={handleSubmit}>
                        <TextInput
                            type="password"
                            placeholder={translate("password")}
                            value={password}
                            isSecret={true}
                            handleValueChange={handlePasswordChange}
                        />
                        <TextInput
                            type="password"
                            placeholder={translate("confirm-password")}
                            value={verifyPassword}
                            isSecret={true}
                            handleValueChange={handleVerifyPasswordChange}
                        />
                        <Button type={'submit'} title={translate("reset-password")} hasBackground className={'mt-10 !w-full !bg-primary text-background hover:!bg-secondary'}/>
                        {areInvalidCredentials && (
                            <WarningAlert message={translate("password-regex")} />
                        )}
                    </form>
                </div>
            </div>
        </div>
    );
}
