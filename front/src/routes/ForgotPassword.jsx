import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import TextInput from "../components/atoms/TextInput.jsx";
import WarningAlert from "../components/atoms/WarningAlert.jsx";
import Button from "../components/atoms/Button.jsx";
import SetUpInstance from '../utils/axios.js';
import { API_FORGOT_PASSWORD_ROUTE } from '../utils/apiRoutes.js';

export default function ForgotPassword() {
    const [email, setEmail] = useState("");    
    const [areInvalidCredentials, setAreInvalidCredentials] = useState(false);
    const navigate = useNavigate();
    const http = SetUpInstance();

    const handleEmailChange = (event) => {
        setEmail(event.target.value);
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        try {
            const userCredentials = {
                email: email,
            };
            await http.post(API_FORGOT_PASSWORD_ROUTE, userCredentials);
            navigate('/login');
        } catch (error) {
            setAreInvalidCredentials(true);
        }
    }

    return (
        <div className="mt-64 bg-background max-sm:mt-28">
            <div className="hero-content flex-col lg:flex-row-reverse">
                <div className="text-center lg:text-left">
                    <h1 className={'text-4xl text-text px-14 max-sm:px-8 ont-bold max-sm:text-xl'}>
                        <b className={'text-color-effect text-4xl font-heading max-sm:text-2xl'}> Baristos</b><br/>
                        <span className={'text-4xl max-sm:text-2xl'}>Forgot your password?</span>
                    </h1>
                    <p className={'text-text max-sm:text-sm px-14 max-sm:px-8'}>
                        Enter your email and we will send you a link to reset your password.
                    </p>
                </div>
                <div className="card shrink-0 w-full max-w-sm shadow-2xl">
                    <form className="card-body" onSubmit={handleSubmit}>
                        <TextInput
                            type="email"
                            placeholder="Email"
                            value={email}
                            handleValueChange={handleEmailChange}
                        />
                        <Button type={'submit'} title="Envoyer un lien de réinitialisation" hasBackground className={'mt-10 !w-full !bg-primary text-background hover:!bg-secondary'}/>
                    </form>
                </div>
                <div id="error-div">
                        {
                            areInvalidCredentials === true ?
                                <WarningAlert message="Vos identifiants sont incorrects" handleClose={() => {
                                    setAreInvalidCredentials(false);
                                }}/>
                                : null
                        }
                    </div>
            </div>
        </div>
    );
}
