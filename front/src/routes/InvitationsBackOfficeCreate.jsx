import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import TextInput from "../components/atoms/TextInput.jsx";
import WarningAlert from "../components/atoms/WarningAlert.jsx";
import Button from "../components/atoms/Button.jsx";
import { useAuth } from "../app/authContext.jsx";
import { useParams } from "react-router-dom";
import SetUpInstance from "../utils/axios.js";

export default function InvitationsBackOfficeCreate() {
  const [email, setEmail] = useState("");
  const [areMissingInfos, setAreMissingInfos] = useState(false);
  const [error, setError] = useState(false);

  const { companySlug } = useParams();
  const { isCompanyAdmin, isLoggedIn } = useAuth();
  const navigate = useNavigate();
  const http = SetUpInstance();

  useEffect(() => {
    if (!isLoggedIn() || !isCompanyAdmin()) {
      navigate('/');
    } 
  }, []);

  const handleEmailChange = (event) => {
    setEmail(event.target.value);
  }

  const handleSubmit = async () => {
    if (!email) {
      setAreMissingInfos(true);
      return;
    }

    const emailRegex = /\S+@\S+\.\S+/;

    if (!emailRegex.test(email)) {
      setError(true);
      return;
    }

    try {      
      await http.post(`/invitations?email=${email}`, { company: `/companies/${companySlug}` });

      navigate(`/${companySlug}/admin/invitations`);
    } catch (error) {
      setError(true);
    }
  }

  return (
    <div className="mt-32 bg-background max-sm:mt-28 w-1/2">
      <h1 className="text-4xl text-color-effect font-heading text-center">Inviter un troublemaker</h1>
      <div className="hero-content flex-col w-full lg:flex-row-reverse">
          <div className="card shrink-0 max-w-sm w-full shadow-2xl bg-surface">
              <div className="card-body">
                  <TextInput
                      placeholder="Email"
                      type="email"
                      value={email}
                      handleValueChange={handleEmailChange}
                    />
                  {areMissingInfos && <WarningAlert message="Veuillez remplir tous les champs" />}
                  {error && <WarningAlert message="Veuillez entrer un email valide, votre invitation doit être à destination d'un troublemaker actif n'ayant pas encore de company" />}
                  <Button 
                      title="Envoyer une invitation"
                      onClick={handleSubmit}
                      hasBackground 
                      className={'!w-full !bg-primary text-background hover:!bg-secondary mt-5'}/>
              </div>
          </div>
      </div>
    </div>
  );

}