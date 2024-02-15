import {createContext, useContext, useState} from "react";

const OrderContext = createContext({
    service: null,
    setService: () => {},
    nextStepName: 'Date',
    setNextStepName: () => {},
    currentStep: 'Prestataire',
    setCurrentStep: () => {},
    provider: null,
    setProvider: () => {},
    appointmentDate: null,
    setAppointmentDate: () => {},
});

export function useOrder() {
    const {nextStepName, setNextStepName, appointmentDate, setAppointmentDate, currentStep, setCurrentStep, provider, setProvider, service, setService} = useContext(OrderContext);
    return {
        service,
        setService,
        nextStepName,
        setNextStepName,
        appointmentDate,
        setAppointmentDate,
        currentStep,
        setCurrentStep,
        provider,
        setProvider
    };
}

export function OrderContextProvider({children}) {
    let [nextStepName, setNextStepName] = useState('Date');
    let [currentStep, setCurrentStep] = useState('Prestataire');
    let [provider, setProvider] = useState(null);
    let [appointmentDate, setAppointmentDate] = useState(null);
    let [service, setService] = useState(null);

    return (
        <OrderContext.Provider value={{
                nextStepName,
                setNextStepName,
                appointmentDate,
                setAppointmentDate,
                currentStep,
                setCurrentStep,
                provider,
                setProvider,
                service,
                setService
            }}>
            {children}
        </OrderContext.Provider>
    )
}