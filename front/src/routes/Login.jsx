import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import TextInput from "../components/atoms/TextInput.jsx";
import WarningAlert from "../components/atoms/WarningAlert.jsx";
import Button from "../components/atoms/Button.jsx";
import { useAuth } from "../app/authContext.jsx";
import {useTranslator} from "../app/translatorContext.jsx";

export default function Login() {
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [areInvalidCredentials, setAreInvalidCredentials] = useState(false);
    const navigate = useNavigate();
    const { login, getMe, goToMyProfile } = useAuth();
    const [hasTriedToLogin, setHasTriedToLogin] = useState(false);
    const {translate} = useTranslator();

    const handleEmailChange = (event) => {
        setEmail(event.target.value);
    };

    const handlePasswordChange = (event) => {
        setPassword(event.target.value);
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        try {
            const userCredentials = {
                email: email,
                password: password,
            };

            await login(userCredentials);
            setHasTriedToLogin(true);
        } catch (error) {
            setAreInvalidCredentials(true); // Gère les erreurs ici si nécessaire
        }
    };

    useEffect(() => {
        const checkLoggedInUser = async () => {
            try {
                await getMe();
                goToMyProfile();
            } catch (error) {
                // do nothing
            }
        }

        if (hasTriedToLogin) {
            checkLoggedInUser();
        }

    }, [hasTriedToLogin]);

    return (
        <div className="mt-64 bg-background max-sm:mt-28">
            <div className="hero-content flex-col lg:flex-row-reverse">
                <div className="text-center lg:text-left">
                    <h1 className={'text-4xl text-text px-14 max-sm:px-8 ont-bold max-sm:text-xl'}>
                        <b className={'text-color-effect text-4xl font-heading max-sm:text-2xl'}> {translate("trouble-a-lordre-public")}</b><br/>
                        {translate("catch-phrase-1")} <br/> {translate("catch-phrase-2")} <b className={'max-sm:text-2xl text-stroke-effect text-4xl font-heading'}>{translate("catch-phrase-3")}</b>
                    </h1>
                </div>
                <div className="card shrink-0 w-full max-w-sm shadow-2xl bg-surface">
                    <form className="card-body" onSubmit={handleSubmit}>
                        <TextInput
                            placeholder={translate("email")}
                            name="email"
                            value={email}
                            handleValueChange={handleEmailChange}
                        />
                        <TextInput
                            placeholder={translate("password")}
                            name="password"
                            value={password}
                            isSecret={true}
                            handleValueChange={handlePasswordChange}
                        />
                        <a href="/forgot-password" className="text-primary text-right block">Mot de passe oublié ?</a>
                        <Button type={'submit'} title={translate("login")} hasBackground className={'mt-10 !w-full !bg-primary text-background hover:!bg-secondary'}/>
                    </form>
                    <div id="error-div">
                        {
                            areInvalidCredentials === true ?
                                <WarningAlert message={translate("wrong-log-info")} handleClose={() => {
                                    setAreInvalidCredentials(false);
                                }}/>
                                : null
                        }
                    </div>
                    <div>
                        <p className="text-center text-text mb-5 mt-2">{translate("no-aacount-yet")} <a href="/register" className="text-primary">{translate("register")}</a></p>
                    </div>
                </div>
            </div>
        </div>
    );
}
