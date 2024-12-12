import styles from './Details.module.scss';

export const Details = ( { summary, children } ) => {
	return (
		<details className={ styles.root }>
			<summary>{ summary }</summary>
			{ children }
		</details>
	);
};
