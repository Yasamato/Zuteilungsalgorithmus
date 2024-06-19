"use client"

import { type Session } from "next-auth"
import { signIn, signOut } from "next-auth/react"
import { Button } from "./Button"

export function LoginButton({
  session,
  className,
  ...props
}: {
  session: Session | null
  className?: string
} & Omit<React.HTMLAttributes<HTMLButtonElement>, "children">) {
  return (
    <Button
      className={"mr-4 rounded p-2 md:order-2 md:mr-0 " + className}
      {...props}
      variant={session ? "red" : "green"}
      onClick={() => {
        if (session) {
          return signOut()
        }
        return signIn()
      }}
    >
      {session ? "Abmelden" : "Anmelden"}
    </Button>
  )
}
