export default function TextInput(props) {
    const {placeholder} = props;
    const {name} = props;
    const value = props.value || "";
    const {isSecret} = props || false;
    const {disabled} = props || false;

    const {handleValueChange} = props;
    return (
        <div className="flex flex-col gap-2">
            <label htmlFor={name} className={'font-medium text-text'}>{placeholder}</label>
            <input
                name={name}
                className="input w-full max-w-xs bg-accent-200 text-text"
                type={isSecret ? "password" : "text"}
                placeholder={placeholder}
                value={value}
                onChange={handleValueChange}
                disabled={disabled}
            />
        </div>
    )
}