import React, {useState} from "react";

export default function MultipleFileInput(props) {
    const {placeholder} = props;
    const {name} = props;
    const {files} = props;
    const {setFiles} = props;
    const {maxFileSize} = props;
    const {accept} = props;
    const {disabled} = props || false;
    const {preview} = props || false;
    const {multiple} = props || false;
    const [error, setError] = useState(false);

    const handleFileChange = (event) => {
        const files = event.target.files;
        if (files[0].size > maxFileSize) {
            setError(true);
            return;
        }
        setFiles(files);
    }

    return (
        <div className="flex flex-col gap-2">
            <label htmlFor={name} className={'font-medium text-text'}>{placeholder}</label>
            {error && (
                <span className="text-error text-sm">Le fichier est trop volumineux</span>
            )}
            <input
                type="file"
                name={name}
                onChange={handleFileChange}
                className="file-input file-input-bordered w-full max-w-xs"
                accept={accept}
                disabled={disabled}
                multiple={multiple}
            />
            {preview && files.length > 0 && (
                <div className="flex flex-row gap-2">
                    {Array.from(files).map((file, index) => {
                        return (
                            <img key={index} src={URL.createObjectURL(file)} alt={file.name} className="w-20 h-20" />
                        )
                    })}
                </div>
            )}
        </div>
    )
}