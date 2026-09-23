@section('metaTitle', 'About Maison Plush Paris')
@section('metaDescription', '')

<x-layout>
    <section>
        <div class="container grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-24 overflow-hidden">
            <aside class="mobile_edges">
                <img src="/img/maison_plush.webp" alt="Maison Plush Paris">
            </aside>
            <article class="">
                <header class="flex items-center relative gap-6 md:pt-28 pb-12">
                    <img src="/img/olga_plush.webp" class="rounded-full"  alt="Olga Plush Paris">
                    <div>
                        <p class="font-medium text-lg">Olga Plush</p>
                        <p>Clothes that you like</p>
                    </div>
                    <img src="/img/sign.svg" alt="Olga Sign" class="absolute bottom-12 left-[280px] max-w-[200px]">
                </header>

                <div class="space-y-6 max-w-[560px]">
                    @if(app()->getLocale() == 'en')
                    <h2 class="font-medium text-xl">HOW DID THE IDEA OF CREATING A BRAND COME INTO MIND?</h2>
                    <p>Olga Plush is not just a stylist but a true architect of style, a master of impeccable taste, and a refined connoisseur of fashion art. Her journey in the fashion world began long before she founded her own brand. Years of working as a stylist have taught her to see beauty in the details, masterfully combining textures, colors, and silhouettes to create perfectly curated capsule wardrobes. She has a unique ability to tell stories through clothing, giving each look depth and character.</p>
                    <p>Paris has become her second home and an endless source of inspiration. For six years, living in the heart of the fashion industry, Olga has immersed herself in its unique vibe—attending exclusive exhibitions, studying the archives of iconic fashion houses, mastering new techniques, and absorbing the philosophy of haute couture. Her talent is not only an innate sense of style but also a continuous pursuit of professional growth, perfection, and the desire to create something greater than just clothing.</p>
                    <p>Her style and unique approach have been recognized by leading fashion publications. Her work has been featured in Vogue, Elle, and many others, and her name has become synonymous with sophistication, bold creativity, and the art of transforming fashion into pure magic.</p>
                    @endif
                    @if(app()->getLocale() == 'fr')
                        <h2 class="font-medium text-xl">Comment L'idée de créer une marque vous est-elle venue ?</h2>
                        <p>Olga Plush n’est pas seulement une styliste, mais une véritable architecte du style, une maître du goût impeccable et une fine connaisseuse de l’art de la mode. Son parcours dans l’univers de la mode a commencé bien avant la création de sa propre marque. Des années d’expérience en tant que styliste lui ont appris à percevoir la beauté dans les moindres détails, à marier avec virtuosité les textures, les couleurs et les silhouettes pour concevoir des garde-robes capsules pensées à la perfection. Elle sait raconter des histoires à travers les vêtements, conférant à chaque look une profondeur et une personnalité uniques.</p>
                        <p>Paris est devenu son deuxième foyer et une source d’inspiration inépuisable. Depuis six ans, en vivant au cœur de l’industrie de la mode, Olga s’imprègne de cette atmosphère unique : elle assiste à des expositions privées, explore les archives des maisons de couture emblématiques, perfectionne de nouvelles techniques et assimile la philosophie de la haute couture. Son talent ne se résume pas à un simple instinct inné ; c’est aussi un engagement constant envers l’excellence, une quête de perfection et une ambition de créer quelque chose de plus grand que de simples vêtements.</p>
                        <p>Son style et son approche unique sont reconnus par les plus grands magazines de mode. Son travail a été publié dans Vogue, Elle et bien d’autres, et son nom est désormais associé à l’élégance, aux choix audacieux et à l’art de transformer la mode en véritable enchantement.</p>
                    @endif
                </div>
            </article>
        </div>
    </section>
</x-layout>
