import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import TextInput from "../components/atoms/TextInput.jsx";
import WarningAlert from "../components/atoms/WarningAlert.jsx";
import Button from "../components/atoms/Button.jsx";
import { useAuth } from "../app/authContext.jsx";
import { useParams } from "react-router-dom";
import SetUpInstance from "../utils/axios.js";

export default function UserAvailabilitiesCreate() {
  const [availability, setAvailability] = useState({ startTime: '', endTime: ''});
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

  const handleStartDateChange = (event) => {
    setAvailability({ ...availability, startTime: event.target.value });
  }

  const handleEndDateChange = (event) => {
    setAvailability({ ...availability, endTime: event.target.value });
  }


  const handleSubmit = async () => {
    if (!availability.startTime || !availability.endTime) {
      setAreMissingInfos(true);
      return;
    }

    if (availability.startTime >= availability.endTime) {
      setError(true);
      return;
    }

    const startDate = new Date(availability.startTime);
    const endDate = new Date(availability.endTime);
    const currentDate = new Date();
    if (startDate < currentDate || endDate < currentDate) {
      setError(true);
      return;
    }

    if (startDate.getDate() !== endDate.getDate()) {
      setError(true);
      return;
    }

    try {
      const data = {
        startTime: availability.startTime,
        endTime: availability.endTime,
        troubleMaker: `/users/${userId}`
      }

      await http.post(`/availabilities`, data);
      navigate(`/profile/${userId}/planning/availabilities`);
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
                      onChange={ (e) => handleStartDateChange(e) }
                  />
                </div>
                <div className="flex flex-col gap-2">
                  <label className={'font-medium text-text'}>{"End time"}</label>
                  <input
                      className="input w-full max-w-xs bg-accent-200 text-text"
                      type="datetime-local"
                      placeholder={"End time"}
                      value={availability.endTime}
                      onChange={ (e) => handleEndDateChange(e) }
                  />
                </div>
            {areMissingInfos && <WarningAlert message="Veuillez remplir tous les champs" />}
            {error && <WarningAlert message="Veuillez mettre une date de fin supérieure à la date de début et au même jour" />}
            <Button 
                title="Créer la disponibilité"
                onClick={handleSubmit}
                hasBackground 
                className={'!w-full !bg-primary text-background hover:!bg-secondary mt-5'}/>
              </div>
          </div>
      </div>
    </div>
  );
}