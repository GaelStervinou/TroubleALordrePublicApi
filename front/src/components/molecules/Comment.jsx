import Rating from "../atoms/Rating.jsx";

export default function Comment({ content, author, date, rate, authorImagePath, isFullWidth = false }) {

    date = new Date(date).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });

    return (
        <article className={`flex flex-col gap-6 p-5 rounded-xl bg-accent-200 min-w-80 ${isFullWidth ? 'w-1/2 max-sm:w-full' : null }`}>
            <div className={'flex gap-4 items-end'}>
                <p className={'text-2xl font-medium mb-[-0.6rem]'}>{rate}</p>
                <Rating value={rate}/>
            </div>
            <p className={isFullWidth ? 'w-full' : 'w-60'}>
                {content}
            </p>
            <div className={'flex gap-4 items-end'}>
                <img
                    src={authorImagePath}
                    alt={'user'}
                    className={'w-12 h-12 rounded-full object-cover'}
                />
                <div className={'flex flex-col'}>
                    <p className={'text-md font-medium whitespace-pre'}>{author}</p>
                    <p className={'text-sm text-gray-400'}>{date}</p>
                </div>
            </div>
        </article>
    );
}