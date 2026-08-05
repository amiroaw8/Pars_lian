import tailwindcss from 'tailwindcss';
import autoprefixer from 'autoprefixer';

const addStandardProperties = () => {
    return {
        postcssPlugin: 'add-standard-properties',
        Rule(rule) {
            let hasDisplayBlock = false;
            let verticalAlignDecl = null;
            rule.walkDecls(decl => {
                if (decl.prop === 'display' && decl.value === 'block') {
                    hasDisplayBlock = true;
                }
                if (decl.prop === 'vertical-align') {
                    verticalAlignDecl = decl;
                }
            });
            if (hasDisplayBlock && verticalAlignDecl) {
                verticalAlignDecl.remove();
            }
        },
        Declaration(decl) {
            if (decl.prop === '-webkit-appearance') {
                const hasStandard = decl.parent.some(d => d.prop === 'appearance');
                if (!hasStandard) {
                    decl.cloneAfter({ prop: 'appearance', value: decl.value });
                }
            }
            if (decl.prop === '-webkit-line-clamp') {
                const hasStandard = decl.parent.some(d => d.prop === 'line-clamp');
                if (!hasStandard) {
                    decl.cloneAfter({ prop: 'line-clamp', value: decl.value });
                }
            }
        }
    }
};
addStandardProperties.postcss = true;

export default {
    plugins: [
        tailwindcss(),
        autoprefixer(),
        addStandardProperties()
    ]
}
