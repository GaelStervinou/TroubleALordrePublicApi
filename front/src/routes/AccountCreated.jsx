import Button from "../components/atoms/Button.jsx";
import { useNavigate } from "react-router-dom";

export default function AccountCreated() {
    const navigate = useNavigate();

    const redirectToHome = () => {
        navigate('/');
    }

    return (
        <div className="mt-64 bg-background max-sm:mt-28">
            <div className="hero-content flex-col lg:flex-row-reverse">
                <div className="text-center lg:text-left">
                    <h2 className={'text-4xl text-text px-14 max-sm:px-8 ont-bold max-sm:text-xl'}>
                        <b className={'text-color-effect text-4xl font-heading max-sm:text-2xl'}> Baristos</b><br/>
                        <span className={'text-4xl max-sm:text-2xl'}>Compte créé</span>
                    </h2>
                    <p className={'text-text max-sm:text-sm px-14 max-sm:px-8'}>
                        Votre compte a été créé avec succès.
                        Vous recevrez un email pour valider votre compte.
                        Cliquez sur le lien dans l'email pour valider votre compte.
                    </p>
                </div>
                <div className="card shrink-0 w-full max-w-sm shadow-2xl">
                    <Button onClick={redirectToHome} title="Retour à l'accueil" hasBackground className={'mt-10 !w-full !bg-primary text-background hover:!bg-secondary'}/>
                </div>
            </div>
        </div>
    );
}
