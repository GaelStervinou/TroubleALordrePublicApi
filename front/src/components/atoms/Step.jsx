export default function Step({ steps, activeStep }) {

    const activeStepIndex = steps.indexOf(activeStep);

    return (
        <ul className="steps w-full">
            {steps.map((step, index) => (
                <li
                    key={index}
                    className={`step max-sm:text-sm ${index <= activeStepIndex ? 'step-primary' : null }`}
                >{step}</li>
            ))}
        </ul>
    );
}