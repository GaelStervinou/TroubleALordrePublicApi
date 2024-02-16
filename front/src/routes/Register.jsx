import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import TextInput from "../components/atoms/TextInput.jsx";
import WarningAlert from "../components/atoms/WarningAlert.jsx";
import Button from "../components/atoms/Button.jsx";
import { useAuth } from "../app/authContext.jsx";
import {useTranslator} from "../app/translatorContext.jsx";

export default function Register() {
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [verifyPassword, setVerifyPassword] = useState("");
    const [firstname, setFirstname] = useState("");
    const [lastname, setLastname] = useState("");
    const [areInvalidCredentials, setAreInvalidCredentials] = useState(false);
    const { register } = useAuth();
    const [hasTriedToRegister, setHasTriedToRegister] = useState(false);
    const navigate = useNavigate();
    const {translate} = useTranslator();

    const handleEmailChange = (event) => {
        setEmail(event.target.value);
    };

    const handlePasswordChange = (event) => {
        setPassword(event.target.value);
    };

    const handleVerifyPasswordChange = (event) => {
        setVerifyPassword(event.target.value);
    }

    const handleFirstnameChange = (event) => {
        setFirstname(event.target.value);
    }

    const handleLastnameChange = (event) => {
        setLastname(event.target.value);
    }

    const handleSubmit = async (event) => {
        event.preventDefault();
        try {
            const userCredentials = {
                email: email,
                password: password,
                verifyPassword: verifyPassword,
                firstname: firstname,
                lastname: lastname,
            };
            register(userCredentials);
            setHasTriedToRegister(true);
        } catch (error) {
            setAreInvalidCredentials(true); // Gère les erreurs ici si nécessaire
        }
    };

    useEffect(() => {
        const waitingForEmailValidation = async () => {
            try {
                navigate('/account-created');
            } catch (error) {
                // do nothing
            }
        };

        if (hasTriedToRegister) {
            waitingForEmailValidation();
        }
    }, [hasTriedToRegister]);

    return (
        <div className="mt-36 bg-background max-sm:mt-28">
            <div className="hero-content flex-col lg:flex-row-reverse">
                <div className="card shrink-0 w-full max-w-sm shadow-2xl bg-surface">
                    <form className="card-body" onSubmit={handleSubmit}>
                        <div className="flex flex-row gap-5">
                            <TextInput
                                type="text"
                                placeholder={translate("firstname")}
                                value={firstname}
                                handleValueChange={handleFirstnameChange}
                            />
                            <TextInput
                                type="text"
                                placeholder="Nom"
                                value={lastname}
                                handleValueChange={handleLastnameChange}
                            />
                        </div>
                        <TextInput
                            type="email"
                            placeholder={translate("email")}
                            value={email}
                            handleValueChange={handleEmailChange}
                        />
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
                        <Button type={'submit'} title={translate("register")} hasBackground className={'mt-10 !w-full !bg-primary text-background hover:!bg-secondary'}/>
                        {areInvalidCredentials && (
                            <WarningAlert message={translate("wrong-log-info")} />
                        )}
                    </form>
                </div>
            </div>
        </div>
    );
}
