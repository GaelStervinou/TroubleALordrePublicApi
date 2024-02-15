import { AiOutlineTwitter, AiOutlineGithub, AiFillInstagram } from 'react-icons/ai';

export default function Footer() {
    return (
        <footer className="flex items-center justify-between py-3 px-16 max-sm:px-8 pt-20 text-gray-400 w-full max-sm:pb-24 absolute bottom-0 z-30">
            <div className="text-sm font-medium">
                <span className={'max-sm:hidden mr-1'}>
                    Copyright
                </span>© 2024 Trouble à l'ordre publique</div>
            <div className="flex space-x-4">
                <a href="lien_vers_instagram" target="_blank" rel="noopener noreferrer"><AiFillInstagram /></a>
                <a href="lien_vers_twitter" target="_blank" rel="noopener noreferrer"><AiOutlineTwitter /></a>
                <a href="lien_vers_github" target="_blank" rel="noopener noreferrer"><AiOutlineGithub /></a>
            </div>
        </footer>
    );
}