import { getElement, useEffect, useState } from "@wordpress/interactivity";

// `data-wp-run`
export const useInView = () => {
  const [inView, setInView] = useState(false);
  useEffect(() => {
    const { ref } = getElement();
    const observer = new IntersectionObserver(([entry]) => {
      setInView(entry.isIntersecting);
    });
    observer.observe(ref);
    return () => ref && observer.unobserve(ref);
  }, []);
  return inView;
};
