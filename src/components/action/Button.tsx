type ButtonVariants =
  | "default"
  | "dark"
  | "light"
  | "green"
  | "red"
  | "yellow"
  | "purple"

const variantMap = {
  default:
    "bg-blue-700 hover:bg-blue-800 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800",
  dark: "bg-gray-800 hover:bg-gray-900 focus:ring-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700",
  light:
    "border border-gray-300 bg-white text-gray-900 hover:bg-gray-100 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:hover:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700",
  green:
    "bg-green-700 hover:bg-green-800 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800",
  red: "bg-red-700 hover:bg-red-800 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900",
  yellow:
    "bg-yellow-400 hover:bg-yellow-500 focus:ring-yellow-300 dark:focus:ring-yellow-900",
  purple:
    "bg-purple-700 hover:bg-purple-800 focus:ring-purple-300 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900",
}

export function Button({
  variant = "default",
  className,
  ...props
}: { variant?: ButtonVariants } & React.HTMLAttributes<HTMLButtonElement>) {
  return (
    <button
      className={
        "rounded-lg px-5 py-2.5 text-sm font-medium text-white focus:outline-none focus:ring-4 " +
        variantMap[variant] +
        " " +
        className
      }
      {...props}
    />
  )
}
