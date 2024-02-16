import { useNavigate } from "react-router-dom";
import Button from "../components/atoms/Button.jsx";
import SetUpInstance from '../utils/axios.js';
import { API_VALIDATE_ACCOUNT_ROUTE } from '../utils/apiRoutes.js';
import {useTranslator} from "../app/translatorContext.jsx";

export default function ValidateAccount() {
    const navigate = useNavigate();
    const http = SetUpInstance();
    const {translate} = useTranslator();

    const handleSubmit = async (event) => {
        event.preventDefault();
        try {
            const token = window.location.pathname.split('/')[2];
            
            http.patch(API_VALIDATE_ACCOUNT_ROUTE + `/${token}`);
            navigate('/login');
        } catch (error) {
            console.error(error);
        }
    }

    return (
        <div className="mt-64 bg-background max-sm:mt-28">
            <div className="hero-content flex-col lg:flex-row-reverse">
                <div className="text-center lg:text-left">
                    <h1 className={'text-4xl text-text px-14 max-sm:px-8 ont-bold max-sm:text-xl'}>
                        <b className={'text-color-effect text-4xl font-heading max-sm:text-2xl'}>Trouble à l'ordre public</b><br/>
                        <span className={'text-4xl max-sm:text-2xl'}>{translate("validate-account")}</span>
                    </h1>
                    <p className={'text-text max-sm:text-sm px-14 max-sm:px-8'}>
                        {translate("validate-account-click")}
                    </p>
                </div>
                <div className="card shrink-0 w-full max-w-sm shadow-2xl">
                    <form className="card-body" onSubmit={handleSubmit}>
                        <Button type={'submit'} title={translate("validate-account")} hasBackground className={'mt-10 !w-full !bg-primary text-background hover:!bg-secondary'}/>
                    </form>
                </div>
            </div>
        </div>
    );
}
