import { useState, useEffect } from "react";

export function useAuth() {

  const [user, setUser] = useState({"email":"zouker24@gmail.com"});
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    // check token here
  }, []);

  return { user, loading };
}