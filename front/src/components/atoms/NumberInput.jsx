export default function NumberInput(props) {
    const {placeholder} = props;
    const {name} = props;
    const value = props.value || "";
    const {disabled} = props || false;
    const {min} = props || 0;
    const {max} = props || 100;
    const {step} = props || 1;

    const {handleValueChange} = props;
    return (
        <div className="flex flex-col gap-2">
            <label htmlFor={name} className={'font-medium text-text'}>{placeholder}</label>
            <input
                name={name}
                className="input w-full max-w-xs bg-accent-200 text-text"
                type="number"
                min={min}
                max={max}
                step={step}
                placeholder={placeholder}
                value={value}
                onChange={handleValueChange}
                disabled={disabled}
            />
        </div>
    )
}