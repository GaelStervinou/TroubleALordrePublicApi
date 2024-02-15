import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import TextInput from "../components/atoms/TextInput.jsx";
import WarningAlert from "../components/atoms/WarningAlert.jsx";
import Button from "../components/atoms/Button.jsx";
import { useAuth } from "../app/authContext.jsx";
import { useParams } from "react-router-dom";
import SetUpInstance from "../utils/axios.js";

export default function UserAvailabilitiesCreate() {
  const [availability, setAvailability] = useState({});
  const [areMissingInfos, setAreMissingInfos] = useState(false);
  const [error, setError] = useState(false);

  const http = SetUpInstance();
  const navigate = useNavigate();
  const { isLoggedIn, isCompanyAdmin } = useAuth();
  const { userId } = useParams();

  useEffect(() => {
    if (!isLoggedIn() && !isCompanyAdmin()) {
      navigate('/');
    } 
  }, []);

  const handleInputChange = (e) => {
    setAvailability({ ...availability, [e.target.name]: e.target.value });
  };

  const handleSubmit = async () => {
    try {
      await http.post(`/users/${userId}/availabilities`, availability);
      navigate(`/users/${userId}/availabilities`);
    } catch (error) {
      console.error(error);
    }
  };

  return (
    <div className="mt-32 bg-background max-sm:mt-28 w-1/2">
      <h1 className="text-4xl text-color-effect font-heading text-center">Créer une disponibilité</h1>
      <div className="hero-content flex-col w-full lg:flex-row-reverse">
          <div className="card shrink-0 max-w-sm w-full shadow-2xl bg-surface">
              <div className="card-body">
                <div className="flex flex-col gap-2">
                  <label className={'font-medium text-text'}>{"Start time"}</label>
                  <input
                      className="input w-full max-w-xs bg-accent-200 text-text"
                      type="datetime-local"
                      placeholder={"Start time"}
                      value={availability.startTime}
                      onChange={handleInputChange}
                  />
                </div>
                <div className="flex flex-col gap-2">
                  <label className={'font-medium text-text'}>{"End time"}</label>
                  <input
                      className="input w-full max-w-xs bg-accent-200 text-text"
                      type="datetime-local"
                      placeholder={"End time"}
                      value={availability.endTime}
                      onChange={handleInputChange}
                  />
                </div>
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