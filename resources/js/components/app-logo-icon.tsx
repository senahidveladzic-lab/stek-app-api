import type { SVGAttributes } from 'react';

export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <svg {...props} viewBox="0 0 280 80" xmlns="http://www.w3.org/2000/svg">
            <text x="4" y="68" fontFamily="'Gulfs Display', Georgia, 'Times New Roman', serif" fontSize="72" fontWeight="900" fill="currentColor" letterSpacing="-2">štek.</text>
        </svg>
    );
}
