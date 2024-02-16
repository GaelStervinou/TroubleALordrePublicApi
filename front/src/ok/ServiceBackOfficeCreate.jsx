import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import TextInput from "../components/atoms/TextInput.jsx";
import NumberInput from "../components/atoms/NumberInput.jsx";
import WarningAlert from "../components/atoms/WarningAlert.jsx";
import Button from "../components/atoms/Button.jsx";
import { useAuth } from "../app/authContext.jsx";
import { useParams } from "react-router-dom";
import { createService } from "../queries/services.js";

export default function ServiceBackOfficeCreate() {
  const [name, setName] = useState("");
  const [description, setDescription] = useState("");
  const [duration, setDuration] = useState(0);
  const [price, setPrice] = useState(0);
  const [areMissingInfos, setAreMissingInfos] = useState(false);
  const [error, setError] = useState(false);

  const { companySlug } = useParams();
  const { isCompanyAdmin, isLoggedIn } = useAuth();
  const navigate = useNavigate();


  useEffect(() => {
    if (!isLoggedIn() || !isCompanyAdmin()) {
      navigate('/');
    } 
  }, []);

  const handleNameChange = (event) => {
    setName(event.target.value);
  }

  const handleDescriptionChange = (event) => {
    setDescription(event.target.value);
  }

  const handleDurationChange = (event) => {
    setDuration(Number(event.target.value));
  }

  const handlePriceChange = (event) => {
    setPrice(Number(event.target.value));
  }

  const handleSubmit = async () => {
    if (!name || !description || !duration || !price) {
      setAreMissingInfos(true);
      return;
    }

    if (name.length < 5 || description.length < 5) {
      setError(true);
      return;
    }

    try {

      const service = {
        name: name,
        description: description,
        duration: duration * 60, // conversion en secondes
        price: price,
        company: `/companies/${companySlug}`
      };

      await createService(service);
      navigate(`/${companySlug}/admin/gestion/services`);

    } catch (error) {
      window.location.href = '/error';
      setError(true);
    }
  };

  return (
    <div className="mt-32 bg-background max-sm:mt-28 w-1/2">
      <h1 className="text-4xl text-color-effect font-heading text-center">Créer un service</h1>
      <div className="hero-content flex-col w-full lg:flex-row-reverse">
          <div className="card shrink-0 max-w-sm w-full shadow-2xl bg-surface">
              <div className="card-body" >
                  <TextInput
                      placeholder="Titre"
                      type="text"
                      value={name}
                      handleValueChange={handleNameChange}
                    />
                  <TextInput
                      placeholder="Description"
                      type="text"
                      value={description}
                      handleValueChange={handleDescriptionChange}
                    />
                  <NumberInput
                      placeholder="Durée (en minutes)"
                      type="number"
                      value={duration}
                      min={5}
                      max={120}
                      step={5}
                      handleValueChange={handleDurationChange}
                    />
                  <NumberInput
                      placeholder="Prix"
                      type="number"
                      value={price}
                      min={1}
                      max={1000}
                      step={0.1}
                      handleValueChange={handlePriceChange}
                    />
                  <div className="pt-4 flex flex-col gap-2">
                    <Button 
                      title="Créer"
                      onClick={handleSubmit}
                      hasBackground 
                      className={'!w-full !bg-primary text-background hover:!bg-secondary'}/>
                    {areMissingInfos && <WarningAlert message="Veuillez remplir tous les champs" />}
                    {error && <WarningAlert message="Erreur lors de la création de l'entreprise" />}
                  </div>
              </div>
          </div>
      </div>
    </div>
  );
}