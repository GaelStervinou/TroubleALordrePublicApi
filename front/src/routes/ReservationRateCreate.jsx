import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import TextInput from "../components/atoms/TextInput.jsx";
import WarningAlert from "../components/atoms/WarningAlert.jsx";
import NumberInput from "../components/atoms/NumberInput.jsx";
import Button from "../components/atoms/Button.jsx";
import { useAuth } from "../app/authContext.jsx";
import { useParams } from "react-router-dom";
import SetUpInstance from "../utils/axios.js";

export default function ReservationRateCreate() {
  const [rate, setRate] = useState(0);
  const [content, setContent] = useState("");
  const [areMissingInfos, setAreMissingInfos] = useState(false);
  const [error, setError] = useState(false);

  const http = SetUpInstance();
  const navigate = useNavigate();
  const { isLoggedIn } = useAuth();

  const { reservationId } = useParams();

  useEffect(() => {
    if (!isLoggedIn()) {
      navigate('/');
    } 
  }, []);

  const handleRateChange = (event) => {
    setRate(Number(event.target.value));
  }

  const handleContentChange = (event) => {
    setContent(event.target.value);
  }

  const handleSubmit = async () => {
    if (!rate || !content) {
      setAreMissingInfos(true);
      return;
    }

    if (rate < 0 || rate > 5) {
      setError(true);
      return;
    }

    try {
      const data = {
        value: rate,
        content: content,
        reservation: `/reservations/${reservationId}`
      };

      console.log(data);
      await http.post(`/rates`, data);

      navigate(`/reservations/${reservationId}`);
    } catch (error) {
      setError(true);
    }
  }

  return (
    <div className="mt-32 bg-background max-sm:mt-28 w-1/2">
      <h1 className="text-4xl text-color-effect font-heading text-center">Ajouter une note</h1>
      <div className="hero-content flex-col w-full lg:flex-row-reverse">
          <div className="card shrink-0 max-w-sm w-full shadow-2xl bg-surface">
              <div className="card-body" >
                  <NumberInput
                      placeholder="Note"
                      min={0}
                      max={5}
                      step={0.5}
                      value={rate}
                      handleValueChange={(event) => handleRateChange(event)}
                  />
                  <TextInput
                      placeholder="Commentaire"
                      value={content}
                      handleValueChange={(event) => handleContentChange(event)}
                  />
                  <div className="pt-4 flex flex-col gap-2">
                    <Button 
                      title="Valider"
                      onClick={handleSubmit}
                      hasBackground 
                      className={'!w-full !bg-primary text-background hover:!bg-secondary'}/>
                    {areMissingInfos && <WarningAlert message="Veuillez remplir tous les champs" />}
                    {error && <WarningAlert message="Erreur lors de la création de la note" />}
                  </div>
              </div>
          </div>
      </div>
    </div>
  );
}